<?php
/**
 * 购物车
 */
namespace app\home\controller;

class Cart extends Common
{

    public function initialize()
    {
        parent::initialize();
        if (!session('user.id'))
        {
            $this->redirect('user/login/index');
        }
    }

    /**
     * 购物车首页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $list = db('shop_cart')->where(['uid'=>session('user.id')])->order('id DESC')->select();
        foreach ($list as $k => &$v) {
            if ($v['goods_norms']) {
                $norms = json_decode($v['goods_norms'], true);
                $norms_string = '';
                foreach ($norms as $kn => $kv) {
                    foreach ($kv as $kk => $vv) {
                        $norms_string .= $kk.':'.$vv.' ';
                    }
                }
                $v['goods_norms'] = $norms_string;
            }
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 操作商品
     */
    public function operation()
    {
        if (request()->isAjax()) {
            $data = request()->post();
            db('shop_cart')->where(['id'=>$data['id']])->update(['number'=>$data['number'],'price'=>$data['price']]);
            return json_encode(['code'=>1]);
        }
    }

    /**
     * 添加购物车
     */
    public function add()
    {
        if (request()->isAjax()) {
            $data = request()->post();
            if (!empty($data['goods_id']) && !empty($data['number']) && !empty($data['price'])) {
                $goods_info = db('shop_goods')->find($data['goods_id']);
                if ($goods_info) {
                    $cart_data = [
                        'goods_id' => $data['goods_id'],
                        'goods_gid' => $data['gid'],
                        'goods_title' => $goods_info['title'],
                        'goods_price' => $data['price'],
                        'goods_norms' => (!empty($data['norms'])) ? json_encode($data['norms']) : '',
                        'goods_image' => $goods_info['pic'],
                        'uid' => session('user.id'),
                        'number' => $data['number'],
                        'price' => $data['price'],
                        'create_time' => time()
                    ];
                    $re = db('shop_cart')->insertGetId($cart_data);
                    if ($re) {
                        return ['code'=>1, 'text'=>'添加成功', 'cart_id'=>$re];
                    }
                }
            }
            return json(['code'=>0, 'text'=>'添加失败']);
        }
    }

    /**
     * 从购物车删除
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $ids = request()->post('ids');
            if ($ids) {
                $re = db('shop_cart')->where('id','in', $ids)->delete();
                if ($re) {
                    return json(['code'=>1, 'text'=>'删除成功']);
                } else {
                    return json(['code'=>0, 'text'=>'删除失败']);
                }
            }
        }
    }
}