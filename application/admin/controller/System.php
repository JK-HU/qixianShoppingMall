<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
class System extends Common
{
    /**
     * 系统设置
     * @param int $sys_id
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function system($sys_id=1){
        $table = db('system');
        if(Request::isAjax()) {
            $data = Request::except('file');
            if($table->where('id',1)->update($data)!==false) {
                savecache('System');
                return json(['code' => 1, 'msg' => '站点设置保存成功!', 'url' => url('system/system')]);
            } else {
                return json(array('code' => 0, 'msg' =>'站点设置保存失败！'));
            }
        }else{
            $system = $table->find($sys_id);
            $this->assign('system', json_encode($system,true));
            return $this->fetch();
        }
    }

    /**
     * 短信设置
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function sms()
    {
        if(Request::isAjax()) {
            $datas = input('post.');
            foreach ($datas as $k=>$v){
                Db::name('config')->where([['name','=',$k],['inc_type','=','sms']])->update(['value'=>$v]);
            }
            return json(['code' => 1, 'msg' => '短信设置成功!', 'url' => url('system/sms')]);
        }else{
            $sms = Db::name('config')->where('inc_type','sms')->select();
            $info = convert_arr_kv($sms,'name','value');
            $this->assign('info', json_encode($info,true));
            return $this->fetch();
        }
    }

    /**
     * 支付设置
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function pay()
    {
        if(Request::isAjax()) {
            $datas = input('post.');
            foreach ($datas as $k=>$v){
                Db::name('config')->where([['name','=',$k],['inc_type','=','pay']])->update(['value'=>$v]);
            }
            return json(['code' => 1, 'msg' => '支付设置成功!', 'url' => url('system/pay')]);
        }else{
            $sms = Db::name('config')->where('inc_type','pay')->select();
            $info = convert_arr_kv($sms,'name','value');
            $cert = explode('/', $info['wxpay_sslcertpath']);
            $cert_count = count($cert);
            $key = explode('/', $info['wxpay_sslkeypath']);
            $key_count = count($key);
            $info['cert_url_show'] = $cert[$cert_count-2].'/'.$cert[$cert_count-1];
            $info['key_url_show'] = $key[$key_count-2].'/'.$key[$key_count-1];
            $this->assign('info', json_encode($info,true));
            return $this->fetch();
        }
    }

    /**
     * 百度地图设置
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function baidu_map()
    {
        if(Request::isAjax()) {
            $datas = input('post.');
            foreach ($datas as $k=>$v){
                Db::name('config')->where([['name','=',$k],['inc_type','=','map']])->update(['value'=>$v]);
            }
            return json(['code' => 1, 'msg' => '百度地图设置成功!', 'url' => url('system/baidu_map')]);
        }else{
            $sms = Db::name('config')->where('inc_type','map')->select();
            $info = convert_arr_kv($sms,'name','value');
            $this->assign('info', json_encode($info,true));
            return $this->fetch();
        }
    }

    /**
     * 会员配置
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function member()
    {
        if(Request::isAjax()) {
            $datas = input('post.');
            foreach ($datas as $k=>$v){
                Db::name('config')->where([['name','=',$k],['inc_type','=','member']])->update(['value'=>$v]);
            }
            return json(['code' => 1, 'msg' => '会员设置成功!', 'url' => url('system/member')]);
        }else{
            $sms = Db::name('config')->where('inc_type','member')->select();
            $info = convert_arr_kv($sms,'name','value');
            $this->assign('info', json_encode($info,true));
            return $this->fetch();
        }
    }

    /**
     * 积分配置
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function integral()
    {
        if(Request::isAjax()) {
            $datas = input('post.');
            foreach ($datas as $k=>$v){
                Db::name('config')->where([['name','=',$k],['inc_type','=','integral']])->update(['value'=>$v]);
            }
            return json(['code' => 1, 'msg' => '积分设置成功!', 'url' => url('system/integral')]);
        }else{
            $sms = Db::name('config')->where('inc_type','integral')->select();
            $info = convert_arr_kv($sms,'name','value');
            $this->assign('info', json_encode($info,true));
            return $this->fetch();
        }
    }

    /**
     * 消息通知
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function notification()
    {
        if(Request::isAjax()) {
            $datas = input('post.');
            foreach ($datas as $k=>$v){
                Db::name('config')->where([['name','=',$k],['inc_type','=','notification']])->update(['value'=>$v]);
            }
            return json(['code' => 1, 'msg' => '消息通知设置成功!', 'url' => url('system/notification')]);
        }else{
            $sms = Db::name('config')->where('inc_type','notification')->select();
            $info = convert_arr_kv($sms,'name','value');
            $this->assign('info', json_encode($info,true));
            return $this->fetch();
        }
    }
}
