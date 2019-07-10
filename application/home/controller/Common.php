<?php
namespace app\home\controller;
use think\Db;
use clt\Leftnav;
use think\Controller;
class Common extends Controller
{
    protected $config;

    protected function initialize()
    {
        $sys = cache('System');
        $this->config = cache('Config');
        $this->assign('sys',$sys);
        $this->assign('config',$this->config);
        //获取控制方法
        $action = request()->action();
        $controller = request()->controller();
        $this->assign('action',($action));
        $this->assign('controller',strtolower($controller));
        define('MODULE_NAME',strtolower($controller));
        define('ACTION_NAME',strtolower($action));
        $this->assign('time', time());
    }

    /**
     * 根据关键字查询返回地区列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRegionList()
    {
        if (request()->isAjax()) {
            $iptval = input('post.iptval');
            $list = db('region')->where('name', 'like', "%{$iptval}%")->where(['type'=>1])->select();
            if ($list) {
                $result['status'] = 1;
                $result['data'] = $list;
                return $result;
            } else {
                $result['status'] = 0;
                return $result;
            }
        }
    }

    /**
     * 返回指定位置广告列表
     * @param $type_id
     * @param int $number
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getBannerList($type_id, $number=5)
    {
        return db('ad')
            ->where(['type_id'=>$type_id, 'open'=>1])
            ->order('sort desc')
            ->limit($number)
            ->select();
    }

    public function _empty()
    {
        return $this->error('空操作，返回上次访问页面中...');
    }
}