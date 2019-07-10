<?php
/**
 * 积分
 */
namespace app\user\controller;

class Integral extends Common
{
    public function index()
    {
        $info = db('users')->field('integral')->find(session('user.id'));
        // 今日获得
        $today_info = db('user_integral_log')
            ->where('create_time', '<', strtotime('+1 day'))
            ->where('create_time', '>', strtotime(date('Y-m-d')))
            ->where('type', '=', 2)
            ->where('uid', '=', session('user.id'))
            ->sum('number');
        // 累计使用
        $total_use = db('user_integral_log')
            ->where(['uid'=>session('user.id'), 'type'=>1])
            ->sum('number');

        $this->assign('integral', $info['integral']);
        $this->assign('today_info', $today_info);
        $this->assign('total_use', $total_use);

        return $this->fetch();
    }

    /**
     * 根据类型返回积分使用记录
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_list()
    {
        $type = input('post.attrs_cur');
        $start = input('post.swiper_a_num');
        $number = input('post.swipersize');
        if ($type == 'getintgral') {
            $type = '2';
        }
        if ($type == 'useintgral') {
            $type = '1';
        }
        $list = db('user_integral_log')
            ->where(['type'=> $type])
            ->limit($start, $number)
            ->select();
        foreach ($list as $k => &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
        }
        return $list;
    }
}