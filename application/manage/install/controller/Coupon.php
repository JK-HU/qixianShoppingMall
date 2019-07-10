<?php
/**
 * 优惠券管理
 */
namespace app\admin\controller;
use app\admin\model\Users as UsersModel;
use think\Db;
use clt\Leftnav;
class Coupon extends Common
{
    /**
     * 优惠券列表
     * @return array|mixed
     */
    public function index(){
        if(request()->isPost()){
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=DB::name('coupon')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }

       return $this->fetch();
    }
    /**
     * 优惠券增加编辑
     * @return array|mixed
     */

    public function edit(){
        if(request()->isPost()){
            $data = input('post.');
            if(!empty($data['id'])){
                $time = explode('~', $data['times']);
                $data['over_time'] = strtotime($time[1]);
                $res = DB::name('coupon')->update($data);
            }else{
                if(!empty($data['times'])){
                    $data['create_time'] = time();
                    $time = explode('~', $data['times']);
                    $data['over_time'] = strtotime($time[1]);
                    $res = DB::name('coupon')->insert($data);
                }
            }
            if($res){
                return $result = ['code'=>0,'msg'=>'操作成功'];
            }else{
                return $result = ['code'=>-1,'msg'=>'操作失败'];
            }
        }
        $id = input('get.id');
        $coupon = DB::name('coupon')->where('id', $id)->find();
        $this->assign('coupon', $coupon);
        return $this->fetch();
    }

    /**
     * 优惠券删除
     * @return array|mixed
     */
    public function delete(){
        db('coupon')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
}