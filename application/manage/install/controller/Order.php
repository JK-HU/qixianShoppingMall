<?php
/**
 * 订单管理
 */
namespace app\admin\controller;
use app\admin\model\Users as UsersModel;
class Order extends Common
{
    /**
     * 订单列表
     * @return array|mixed
     */
    public function index()
    {
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('shop_order')->alias('o')
                ->field('o.*, u.username')
                ->join('users u', 'o.uid = u.id', 'LEFT')
                ->where('o.order_id','like',"%".$key."%")
                ->order('id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:s',$v['create_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    /**
     * 订单编辑
     * @return array|mixed
     */
    public function edit($id='')
    {
        if(request()->isPost()){
            $data = input('post.');
            if(empty($data['send_status'])){
                return $result = ['code'=>0,'msg'=>'请选择发货状态!'];
            }
            if(!empty($data['id'])){
                if($data['send_status'] == 1){
                    $data_send['code'] = $data['code'];
                    $data_send['express_id'] = $data['express_id'];
                    $data_send['order_id'] = $data['order_id'];
                    $data_send['create_time'] = time();
                    $data_send['id'] = $data['id'];
                    if(empty($data['code'])){
                        return $result = ['code'=>0,'msg'=>'请选择订单编号!'];
                    }
                    $res = db('express_code')->update($data_send);
                    db('shop_order')->where('id', $data['order_id'])->update(['send_status' => $data['send_status'], 'send_id' => $data['id']]);
                }else if($data['send_status'] == 2){
                    $res = db('shop_order')->where('id', $data['order_id'])->update(['send_status' => $data['send_status']]);
                }
            }else{
                if($data['send_status'] == 1){
                    $data_send['code'] = $data['code'];
                    $data_send['express_id'] = $data['express_id'];
                    $data_send['order_id'] = $data['order_id'];
                    $data_send['create_time'] = time();
                    if(empty($data['code'])){
                        return $result = ['code'=>0,'msg'=>'请选择订单编号!'];
                    }
                    $cid = db('express_code')->insertGetId($data_send);
                    $res = db('shop_order')->where('id', $data['order_id'])->update(['send_status' => $data['send_status'], 'send_id' => $cid]);
                }else if($data['send_status'] == 2){
                    $res = db('shop_order')->where('id', $data['order_id'])->update(['send_status' => $data['send_status']]);
                }
            }

            if($res){
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
     */
    public function orderDel()
    {
        db('shop_order')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    /**
     * 批量删除
     * @return array|mixed
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
}