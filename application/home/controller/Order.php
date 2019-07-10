<?php
/**
 * 订单控制器
 */
namespace app\home\controller;

use app\common\model\Mycoupon;
use app\user\model\Users;

class Order extends Common
{

    protected function initialize()
    {
        parent::initialize();
        if (!session('user.id'))
        {
            $this->redirect('user/login/index');
        }
    }

    /**
     * 下单操作
     * @param $id
     * @param string $type
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function add($id, $type='goods')
    {
        if (request()->isPost())
        {
            $data = request()->post();
            // 处理图片
            $images = '';
            if (isset($data['images'])) {
                $images = implode(',', $data['images']);
                unset($data['images']);
            }
            // 处理地址信息
            $address_id = 0;
            if (!isset($data['address_id'])) {
                $address_array = explode(' ', $data['address_wrap']);
                $address_info = [
                    'uid' => session('user.id'),
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address']
                ];
                $is_address_info = db('user_address')->where($address_info)->field('id')->find();
                if (!$is_address_info) {
                    $address_info['create_time'] = time();
                    isset($address_array[0]) && $address_info['province'] = $address_array[0];
                    isset($address_array[1]) && $address_info['city'] = $address_array[1];
                    isset($address_array[2]) && $address_info['district'] = $address_array[2];
                    $address_id = db('user_address')->insertGetId($address_info);
                } else {
                    $address_id = $is_address_info['id'];
                }
            }
            // var_dump($data);die;
            // 处理使用积分
            if ($data['integral'] > 0) {
                $user = new Users();
                $user_info = $user->field('integral')->find(session('user.id'));
                if ($user_info['integral'] >= $data['integral']) {
                    // 扣除积分
                    $user_info->save([
                        'integral' => $user_info['integral']-$data['integral']
                    ]);
                    // 记录使用情况
                    db('user_integral_log')->insert([
                        'number' => $data['integral'],
                        'uid' => session('user.id'),
                        'type' => 1,
                        'reason' => '购买商品时抵扣',
                        'create_time' => time()
                    ]);
                } else {
                    echo json_encode(['code'=>0, 'text'=>'积分异常，请重新下单操作']);die;
                }
            }
            // 处理使用优惠券
            if ($data['coupon'] > 0) {
                $mycoupon = new Mycoupon();
                $mycoupon_info = $mycoupon->find($data['coupon']);
                if ($mycoupon_info) {
                    $mycoupon_info->save([
                        'status' => 1,
                        'use_time' => time()
                    ]);
                } else {
                    echo json_encode(['code'=>0, 'text'=>'该优惠券不存在，请重新下单操作']);die;
                }
            }
            // 重组插入数据
            $order_data = [
                'order_id' => date('YmdHis').mt_rand(10000,99999),
                'uid' => session('user.id'),
                'g_id' => $data['goods_id'],
                'norms' => $data['goods_norms'],
                'name' => $data['order_name'],
                'money' => $data['money'],
                'remark' => $data['remark'],
                // 'money' => 0.01,
                'type_id' => $data['type_id'],
                'num' => $data['num'],
                'title' => $data['goods_title'],
                'images' => $images,
                'address_id' => $address_id,
                'create_time' => time(),
                'status' => 1,
                'make_time' => $data['ptime']
            ];
            $order_id = db('shop_order')->insertGetId($order_data);
            if ($order_id) {
                // 下单成功，删除购物车
                if ($data['the_type'] == 'cart') {
                    db('shop_cart')->delete($data['the_id']);
                }
                echo json_encode(['code'=>1, 'order_id'=>$order_id, 'text'=>'下单成功']);die;
            } else {
                echo json_encode(['code'=>0, 'text'=>'下单失败，请联系管理员操作']);die;
            }
        }
        if ($id>0)
        {
            // 用户实时信息
            $user_info = db('users')->find(session('user.id'));
            $user_integral = $user_info['integral'];
            // 统一化数组处理
            $ids = explode(',', $id);
            // 根据直接商品下单和购物车进行数据分析处理
            $all_goods_price = 0;
            $goods_infos = [];
            $only_goods = true;
            if ($type == 'cart') {
                // 根据购物车存储的分析出的商品分类与信息
                foreach ($ids as $k => $v)
                {
                    $cart_info = db('shop_cart')->where(['uid'=>session('user.id'), 'id'=>$v])->find();
                    $goods_info = db('shop_goods')->find($cart_info['goods_id']);
                    $goods_infos[$k] = $cart_info;
                    $goods_infos[$k]['money'] = $cart_info['price'];
                    $goods_infos[$k]['number'] = $cart_info['number'];
                    // 数量x价格
                    $goods_price = $cart_info['price']*$cart_info['number'];
                    $all_goods_price += $goods_price;
                    // 运费
                    $goods_infos[$k]['is_free_shipping'] = $goods_info['is_free_shipping'];
                    $goods_infos[$k]['shipping_price'] = $goods_info['shipping_price'];
                    if ($goods_info['is_free_shipping'] == '0')  $all_goods_price += $goods_info['shipping_price'];
                    // 处理规格
                    $norms = '';
                    if (!empty($cart_info['goods_norms'])) {
                        $norms_array = json_decode($cart_info['goods_norms'], true);
                        $norms = '';
                        foreach ($norms_array as $nk => $nv) {
                            foreach ($nv as $nnk => $nnv) {
                                $norms .= $nnk . ':' . $nnv . ' ';
                            }
                        }
                    }
                    $goods_infos[$k]['norms'] = $norms;
                    $goods_infos[$k]['title'] = $cart_info['goods_title'];
                    $goods_infos[$k]['pic'] = $cart_info['goods_image'];
                    $goods_infos[$k]['type'] = db('shop_goods_type')->where('id', $cart_info['goods_gid'])->find();
                    $goods_infos[$k]['type_id'] = $goods_infos[$k]['type']['type_id'];
                    // 如果订单中存在服务类商品
                    if ($goods_infos[$k]['type']['type_id'] == 1) {
                        $only_goods = false;
                    }
                }
            } else {
                // 从商品信息中分析出的商品与分类信息
                foreach ($ids as $k => $v)
                {
                    $goods_info = db('shop_goods')->find($id);
                    $goods_price = the_function_price($goods_info);
                    $all_goods_price += $goods_price;
                    $goods_infos[$k] = $goods_info;
                    $goods_infos[$k]['money'] = $goods_price;
                    $goods_infos[$k]['number'] = 1;
                    $goods_infos[$k]['norms'] = '';
                    $goods_infos[$k]['type'] = db('shop_goods_type')->where('id', $goods_info['gid'])->find();
                    $goods_infos[$k]['type_id'] = $goods_infos[$k]['type']['type_id'];
                    // 如果订单中存在服务类商品
                    if ($goods_infos[$k]['type']['type_id'] == 1) {
                        $only_goods = false;
                    }
                }
            }

            // 积分抵扣 - 根据系统中设定的规则，计算出本单最高抵扣价格
            $consume_integral = $this->config['consume_integral'];
            // --- 价格
            $consume_integral_price = $user_integral*$consume_integral;
            // --- 最多可抵扣积分
            if ($consume_integral_price > $all_goods_price) { // 最多只能抵扣全部价格
                $consume_integral_price = $all_goods_price;
                $user_use_integral = $consume_integral_price/$consume_integral;
            } else { // 积分只能抵扣一部分价格时
                $user_use_integral = $user_integral;
            }

            // 优惠券抵扣 - 查询符合当前价格的优惠券，从高往低
            $mycoupon = new Mycoupon();
            $coupon_info = $mycoupon->alias('mc')
                ->join('coupon c', 'mc.cid=c.id', 'LEFT')
                ->where(['mc.uid'=>session('user.id'), 'mc.status'=>0])
                ->where('start_time', '<', time())
                ->where('over_time', '>', time())
                ->where('satisfy_price', '>=', $consume_integral_price)
                ->order('c.satisfy_price DESC')
                ->field('c.*, mc.id as mcid')
                ->find();

            $this->assign('the_id', $id);
            $this->assign('the_type', $type);
            $this->assign('goods_infos', $goods_infos);
            $this->assign('user_use_integral', $user_use_integral);
            $this->assign('all_goods_price', $all_goods_price);
            $this->assign('consume_integral_price', $consume_integral_price);
            $this->assign('coupon_info', $coupon_info);
            $this->assign('only_goods', $only_goods);

            return $this->fetch();
        }
    }

    /**
     * 选择支付页面
     * @param $id
     * @return mixed
     */
    public function choose($id)
    {
        $this->assign('order_id', $id);
        return $this->fetch();
    }

    /**
     * 支付成功页面
     * @param $status
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function success($status)
    {
        $recommended_list = db('shop_goods')->where(['is_recommended'=>1])->order('sort desc')->limit(2)->select();
        $this->assign('recommended_list', $recommended_list);
        $this->assign('status', $status);

        return $this->fetch();
    }

}