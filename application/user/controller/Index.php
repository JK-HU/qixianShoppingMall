<?php
namespace app\user\controller;
use think\Input;
class Index extends Common{
    protected function initialize()
    {
        parent::initialize();
    }
    public function index()
    {
        $coupon_number = 0;
        if (session('?user')) {
            $user = session('user');
            // 是否是会员
            $vip_info = db('user_vip')->where(['uid'=>$user['id']])->find();
            $this->assign('vip_info', $vip_info);
            // 用户没有使用的优惠券数量
            $coupon_number = db('mycoupon')
                ->alias('mc')
                ->join('coupon c', 'mc.cid=c.id', 'LEFT')
                ->where(['mc.status'=>0])
                ->where('c.start_time', '<', time())
                ->where('c.over_time', '>', time())
                ->count();
        } else {
            $user = false;
        }
        $user['money'] = 0;
        $user['coupon'] = $coupon_number;
        $this->assign('user', $user);
        $this->assign('title','会员中心');
        return $this->fetch();
    }

    /**
     * 协议
     */
    public function agreement()
    {
        return $this->fetch();
    }
}