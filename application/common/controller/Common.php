<?php
namespace app\common\controller;
use think\Controller;
class Common extends Controller
{
    protected $config;
    protected $cache_model;

    public function initialize()
    {
        define('MODULE_NAME',strtolower(request()->controller()));
        define('ACTION_NAME',strtolower(request()->action()));
        // 权限管理
        $this->cache_model = array('Config');
        foreach ($this->cache_model as $r)
        {
            if (!cache($r))
            {
                savecache($r);
            }
        }
        // 系统配置
        $this->config = cache('Config');
    }
    // 空操作
    public function _empty(){
        return $this->error('空操作，返回上次访问页面中...');
    }
}
