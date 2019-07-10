<?php
namespace app\admin\controller;
use app\common\model\ShopOrder;
use app\admin\model\Users;
use app\common\model\ShopOrderEvaluate;
use app\user\model\UserVip;
use think\Db;
use think\facade\Env;
class Index extends Common
{
    public function initialize(){
        parent::initialize();
    }
    public function index(){
        //导航
        // 获取缓存数据
        $authRule = cache('authRule');
        if(!$authRule){
            $authRule = db('auth_rule')->where('menustatus=1')->order('sort')->select();
            cache('authRule', $authRule, 3600);
       }
        //声明数组
        $menus = array();
        foreach ($authRule as $key=>$val){
            $authRule[$key]['href'] = url($val['href']);
            if($val['pid']==0){
                if(session('aid')!=1){
                    if(in_array($val['id'],$this->adminRules)){
                        $menus[] = $val;
                    }
                }else{
                    $menus[] = $val;
                }
            }
        }
        foreach ($menus as $k=>$v){
            foreach ($authRule as $kk=>$vv){
                if($v['id']==$vv['pid']){
                    if(session('aid')!=1) {
                        if (in_array($vv['id'], $this->adminRules)) {
                            $menus[$k]['children'][] = $vv;
                        }
                    }else{
                        $menus[$k]['children'][] = $vv;
                    }
                }
            }
        }
        $this->assign('menus',json_encode($menus,true));
        return $this->fetch();
    }
    public function main(){
        $order = new ShopOrder();
        $user = new Users();
        $user_vip = new UserVip();
        // 统计
        // - 今日订单总数
        $today_order = $order
            ->whereTime('create_time', 'between', [mktime(0,0,0,date('m'),date('d'),date('Y')), mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1])
            ->count();
        // - 今日销售总额
        $today_money = $order
            ->where('status', '>', 1)
            ->whereTime('create_time', 'between', [mktime(0,0,0,date('m'),date('d'),date('Y')), mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1])
            ->sum('money');
        // - 昨日销售总额
        $yesterday_money = $order
            ->where('status', '>', 1)
            ->whereTime('create_time', 'between', [mktime(0,0,0,date('m'),date('d')-1,date('Y')), mktime(0,0,0,date('m'),date('d'),date('Y'))-1])
            ->sum('money');
        // - 近7天销售总额
        $week_money = $order
            ->where('status', '>', 1)
            ->whereTime('create_time', 'between', [mktime(0,0,0,date('m'),date('d')-7,date('Y')), mktime(0,0,0,date('m'),date('d'),date('Y'))-1])
            ->sum('money');
        // - 待付款订单
        $order1 = $order->where(['status'=>1])->count();
        // - 待处理订单
        $order2 = $order->where(['status'=>2])->count();
        // - 已完成订单
        $order3 = $order->where(['status'=>4])->count();
        // - 今日新增用户
        $today_user = $user
            ->whereTime('reg_time', 'between', [mktime(0,0,0,date('m'),date('d'),date('Y')), mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1])
            ->count();
        // - 昨日新增用户
        $yesterday_user = $user
            ->whereTime('reg_time', 'between', [mktime(0,0,0,date('m'),date('d')-1,date('Y')), mktime(0,0,0,date('m'),date('d'),date('Y'))-1])
            ->count();
        // - 近7天新增用户
        $week_user = $user
            ->whereTime('reg_time', 'between', [mktime(0,0,0,date('m'),date('d')-7,date('Y')), mktime(0,0,0,date('m'),date('d'),date('Y'))-1])
            ->count();
        // - 本月新增用户
        $month_user = $user
            ->whereTime('reg_time', 'between', [mktime(0,0,0,date('m'),1,date('Y')),mktime(23,59,59,date('m'),date('t'),date('Y'))])
            ->count();
        // - 用户总数
        $total_user = $user->count();
        // - 会员用户总数
        $member_user = $user_vip->count();
        $statistics = [
            'today_order' => $today_order,
            'today_money' => $today_money,
            'yesterday_money' => $yesterday_money,
            'week_money' => $week_money,
            'order1' => $order1,
            'order2' => $order2,
            'order3' => $order3,
            'today_user' => $today_user,
            'yesterday_user' => $yesterday_user,
            'week_user' => $week_user,
            'month_user' => $month_user,
            'total_user' => $total_user,
            'member_user' => $member_user
        ];
        $this->assign('statistics', $statistics);
        // 配置
        $version = Db::query('SELECT VERSION() AS ver');
        $config  = [
            'url'             => $_SERVER['HTTP_HOST'],
            'document_root'   => $_SERVER['DOCUMENT_ROOT'],
            'server_os'       => PHP_OS,
            'server_port'     => $_SERVER['SERVER_PORT'],
            'server_ip'       => $_SERVER['SERVER_ADDR'],
            'server_soft'     => $_SERVER['SERVER_SOFTWARE'],
            'php_version'     => PHP_VERSION,
            'mysql_version'   => $version[0]['ver'],
            'max_upload_size' => ini_get('upload_max_filesize')
        ];
        $this->assign('config', $config);
        return $this->fetch();
    }
    public function navbar(){
        return $this->fetch();
    }
    public function nav(){
        return $this->fetch();
    }

    /**
     * 清理缓存
     * @return mixed
     */
    public function clear()
    {
        $runtime_path = Env::get('runtime_path');
        if (dir_file_delete($runtime_path)) {
            $result['info'] = '清除缓存成功!';
            $result['status'] = 1;
        } else {
            $result['info'] = '清除缓存失败!';
            $result['status'] = 0;
        }
        $result['url'] = url('admin/index/index');
        return $result;
    }

    private function _deleteDir($R)
    {
        $handle = opendir($R);
        while (($item = readdir($handle)) !== false) {
            if ($item != '.' and $item != '..') {
                if (is_dir($R . '/' . $item)) {
                    $this->_deleteDir($R . '/' . $item);
                } else {
                    if (!unlink($R . '/' . $item))
                        die('error!');
                }
            }
        }
        closedir($handle);
        return rmdir($R);
    }

    //退出登陆
    public function logout(){
        session(null);
        $this->redirect('login/index');
    }
    
}
