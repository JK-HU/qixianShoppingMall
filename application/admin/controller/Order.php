<?php
/**
 * 订单管理
 */
namespace app\admin\controller;
use app\admin\model\Users as UsersModel;
use app\common\model\ShopOrderEvaluate;

class Order extends Common
{
    /**
     * 待支付订单
     * @return mixed
     */
    public function status1()
    {
        $this->assign('status', 1);
        return $this->fetch('order/index');
    }

    /**
     * 已支付，待发货订单
     * @return mixed
     */
    public function status2()
    {
        $this->assign('status', 2);
        return $this->fetch('order/index');
    }

    /**
     * 已发货，待确认收货订单
     * @return mixed
     */
    public function status3()
    {
        $this->assign('status', 3);
        return $this->fetch('order/index');
    }

    /**
     * 已确认收货，待评价订单
     * @return mixed
     */
    public function status4()
    {
        $this->assign('status', 4);
        return $this->fetch('order/index');
    }

    /**
     * 已评价订单
     * @return mixed
     */
    public function status5()
    {
        $this->assign('status', 5);
        return $this->fetch('order/index');
    }

    /**
     * 所有订单列表
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if(request()->isPost())
        {
            $key = input('post.key');
            $page = input('page') ? input('page') : 1;
            $pageSize = input('limit') ? input('limit') : config('pageSize');
            $status = input('post.status') ? input('post.status') : 0;
            $condition = [];
            if ($status) {
                $condition['o.status'] = $status;
            }
            $list = db('shop_order')->alias('o')
                ->field('o.*, u.username,u.mobile')
                ->join('users u', 'o.uid = u.id', 'LEFT')
                ->where('o.order_id','like',"%".$key."%")
                ->where($condition)
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v) {
                $list['data'][$k]['create_time'] = date('Y-m-d H:s',$v['create_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    /**
     * 商品详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        $id = input('get.id');
        $order = db('shop_order')
            ->alias('o')
            ->join('users u', 'o.uid=u.id')
            ->join('user_address ua', 'o.address_id=ua.id')
            ->join('express_code ec', 'o.send_id=ec.id', 'LEFT')
            ->join('express e', 'ec.express_id=e.id', 'LEFT')
            ->field('o.*, u.mobile,u.username, ua.name as addr_name, ua.phone as addr_phone, ua.province as addr_province, ua.city as addr_city, ua.district as addr_district, ua.address as addr_address, e.name as express_name, ec.code as express_code')
            ->where('o.id', $id)
            ->find();

        $this->assign('order', $order);
        return $this->fetch();
    }

    /**
     * 订单编辑
     * @param string $id
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit($id='')
    {
        if(request()->isPost()){
            $data = input('post.');
            if(empty($data['send_type'])){
                return $result = ['code'=>0,'msg'=>'请选择发货状态!'];
            }
            if(!empty($data['id']))
            {
                if ($data['send_type'] == 1)
                {
                    $data_send['code'] = $data['code'];
                    $data_send['express_id'] = $data['express_id'];
                    $data_send['order_id'] = $data['order_id'];
                    $data_send['create_time'] = time();
                    $data_send['id'] = $data['id'];
                    if(empty($data['code'])){
                        return $result = ['code'=>0,'msg'=>'请选择订单编号!'];
                    }
                    $res = db('express_code')->update($data_send);
                    db('shop_order')
                        ->where('id', $data['order_id'])
                        ->update([
                            'status'=>3,
                            'send_id' => $data['id'],
                            'send_type' => $data['send_type'],
                        ]);
                }
                else if($data['send_type'] == 2)
                {
                    $res = db('shop_order')
                        ->where('id', $data['order_id'])
                        ->update([
                            'status'=>3,
                            'send_type' => $data['send_type']
                        ]);
                }
            } else {
                if ($data['send_type'] == 1)
                {
                    $data_send['code'] = $data['code'];
                    $data_send['express_id'] = $data['express_id'];
                    $data_send['order_id'] = $data['order_id'];
                    $data_send['create_time'] = time();
                    if(empty($data['code'])){
                        return $result = ['code'=>0,'msg'=>'请选择订单编号!'];
                    }
                    $cid = db('express_code')->insertGetId($data_send);
                    $res = db('shop_order')
                        ->where('id', $data['order_id'])
                        ->update([
                            'status'=>3,
                            'send_type' => $data['send_type'],
                            'send_id' => $cid
                        ]);
                } else if($data['send_type'] == 2) {
                    $res = db('shop_order')
                        ->where('id', $data['order_id'])
                        ->update([
                            'status'=>3,
                            'send_type' => $data['send_type']
                        ]);
                }
            }

            if($res !== false){
                return $result = ['code'=>1,'msg'=>'提交成功!'];
            }else{
                return $result = ['code'=>0,'msg'=>'提交失败!'];
            }
        }else{
            $express = db('express')->select();
            $express_code = db('express_code')->where('order_id', $id)->find();
            $order = db('shop_order')->where('id', $id)->find();
            $this->assign('express_code', $express_code);
            $this->assign('order', $order);
            $this->assign('express', $express);
            return $this->fetch();
        }
    }

    /**
     * 订单删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function orderDel()
    {
        db('shop_order')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }

    /**
     * 批量删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delall()
    {
        $map[] =array('id','IN',input('param.ids/a'));
        db('shop_order')->where($map)->delete();
        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        return $result;
    }

    /**
     * 评价列表
     * @param int $id
     * @param int $order_id
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function evaluateList($id=0, $order_id=0)
    {
        if (request()->isAjax()) {
            $id=input('post.$id');
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $shop_order_evaluate = new ShopOrderEvaluate();
            $list = $shop_order_evaluate->get_list($id, $pageSize, $page, $key);
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        if ($id) {
            $this->assign('id', $id);
            $this->assign('order_id', $order_id);
            return $this->fetch();
        }
    }
}