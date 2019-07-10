<?php
namespace app\user\controller;
use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use think\Db;
class Coupon extends Common
{
    /**
     * 优惠券列表
     */
    public function index(){
        $uid = session('user.id');
        if(request()->isPost()){
            $status = input('post.status');
            $start = input('post.swipernum'); // 开始id
            $number = input('post.onloadnum'); // 获取数量
            $where[] = ['uid', '=', $uid];

            if(isset($status) && $status != 2){
                $where[] = ['mc.status', '=', $status];
                $where[] = ['c.over_time', '>', time()];
            }

            if(!empty($status) && $status == 2){
                $where[] = ['c.over_time', '<', time()];
            }
            $coupon_list = DB::name('mycoupon')->alias('mc')
                ->field('c.*, mc.status as is_status, mc.use_time')
                ->join('coupon c', 'mc.cid = c.id', 'LEFT')
                ->where($where)
                ->limit($start, $number)
                ->select();

            foreach($coupon_list  as  $key => &$val){
                if($val['over_time'] < time()){
                    $val['status_name'] = '已过期';
                }
                if($val['over_time'] > time() && $val['status'] == 0){
                    $val['status_name'] = '未使用';
                }
                if($val['over_time'] > time() && $val['status'] == 1){
                    $val['status_name'] = '已使用';
                }
                $val['over_time'] = date('Y-m-d H:i:s', $val['over_time']);
                $val['time'] = time();
                $val['use_time'] = date('Y-m-d H:i:s', $val['use_time']);
            }
            return array('code'=>0,'data' => $coupon_list);
        }
        return $this->fetch();
    }

    /**
     * 异步获取当前优惠券列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        if(request()->isPost())
        {
            $start = input('post.swipersize'); // 开始id
            $number = input('post.onloadnum'); // 获取数量

            $coupon_list = DB::name('coupon')->alias('c')
                ->join('mycoupon mc', 'mc.cid=c.id and uid='.session('user.id'), 'LEFT')
                ->where(['c.status'=>1])
                ->where('c.start_time', '<', time())
                ->where('c.over_time', '>', time())
                ->field('c.*, mc.status as is_status, mc.use_time, mc.id as mcid')
                ->limit($start, $number)
                ->select();

            foreach ($coupon_list as $k => &$v) {
                $num = db('mycoupon')->where(['cid'=>$v['id']])->count();
                if ($num >= $v) {
                    unset($coupon_list[$k]);
                }
                $v['start_time'] = date('Y-m-d H:i:s', $v['start_time']);
                $v['over_time'] = date('Y-m-d H:i:s', $v['over_time']);
            }
            return ['code'=>1, 'list'=>$coupon_list];
        }
        return $this->fetch();
    }

    /**
     * 异步操作 - 领取优惠券
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCoupon()
    {
        if (request()->isAjax())
        {
            $data = input('post.');
            $is_have = db('mycoupon')->where(['uid'=>session('user.id'), 'cid'=>$data['id']])->find();
            if ($is_have) {
                return ['code'=>0, 'text'=>'该优惠券已经被领取过'];
            }
            $get_data = [
                'uid' => session('user.id'),
                'cid' => $data['id'],
                'get_time' => time(),
                'status' => 0
            ];
            $result = db('mycoupon')->insert($get_data);
            if ($result) {
                return ['code'=>1, 'text'=>'已领取'];
            } else {
                return ['code'=>0, 'text'=>'领取失败'];
            }
        }
    }

}