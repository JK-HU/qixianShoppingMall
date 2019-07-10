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
     * @throws \think\exception\DbException
     */
    public function index(){
        if(request()->isPost()){
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=DB::name('coupon')->alias('c')
                ->join('mycoupon mc', 'mc.cid=c.id', 'LEFT')
                ->field('c.*, count(*) as now_num')
                ->group('mc.cid')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
                $list['data'][$k]['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
                $list['data'][$k]['over_time'] = date('Y-m-d H:i:s',$v['over_time']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }

       return $this->fetch();
    }

    /**
     * 优惠券增加编辑
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */

    public function edit()
    {
        if(request()->isPost())
        {
            $data = input('post.');
            if(!empty($data['times'])) {
                $times = get_start_end_time($data['times']);
                $data['start_time'] = $times['start'];
                $data['over_time'] = $times['end'];
                unset($data['times']);
                if(!empty($data['id'])) {
                    $res = DB::name('coupon')->update($data);
                }else{
                    $data['create_time'] = time();
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
        $coupon['times'] = set_start_end_time($coupon['start_time'], $coupon['over_time']);
        $this->assign('coupon', $coupon);
        return $this->fetch();
    }

    /**
     * 优惠券删除
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete(){
        db('coupon')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
}