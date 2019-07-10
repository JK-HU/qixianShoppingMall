<?php
/**
 * 会员
 */
namespace app\user\controller;

class Member extends Common
{
    public function index()
    {
        $config = cache('Config');
        $this->assign('config', $config);

        return $this->fetch();
    }

    /**
     * 选择支付页面
     * @param $id
     * @return mixed
     */
    public function choose()
    {
        $config = cache('Config');
        // 创建订单
        $order_data = [
            'order_id' => 'member'.date('YmdHis').mt_rand(10000,99999),
            'uid' => session('user.id'),
            'name' => '会员充值'.$config['member_month_price'].'/月',
            'money' => $config['member_month_price'],
            'create_time' => time(),
            'status' => 1,
        ];
        $id = db('user_vip_order')->insertGetId($order_data);

        $this->assign('order_id', $id);

        return $this->fetch();
    }

    /**
     * 支付成功页面
     */
    public function success()
    {
        return $this->fetch();
    }
}