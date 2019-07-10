<?php
namespace app\user\controller;
use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
class Login extends Controller{
    protected $sys;
    protected $config;

    public function initialize()
    {
        if (session('user.id'))
        {
            $this->redirect('index/index');
        }
        $this->sys = cache('System');
        $this->config = cache('Config');
        $this->assign('sys',$this->sys);
        $this->assign('config',$this->config);
    }

    /**
     * 登录
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if (request()->isPost())
        {
            $table = db('users');
            $username = input('username');
            $password = input('password');
            if (!$username || !$password)
            {
                return array('code'=>0,'msg'=>'请填写账号或密码');
            }
            $user = $table->where("mobile","=",$username)->find();
            if (!$user)
            {
                return array('code'=>0,'msg'=>'账号不存在!');
            } elseif (md5($password) != $user['password']) {
                return array('code'=>0,'msg'=>'密码错误!');
            } elseif ($user['is_lock'] == 1) {
                return array('code'=>0,'msg'=>'账号异常已被锁定！！！');
            } else {
                $sessionUser = $table->where("id",'=',$user['id'])->find();
                $openid = db('oauth')->where([['uid','=',$user['id']],['type','=','qq']])->value('openid');
                if ($openid)
                {
                    $sessionUser['qq'] = 1;
                }
                session('user',$sessionUser);
                return array('code'=>1,'msg'=>'登录成功','url' => url('index/index'));
            }
        } else {
            // 登录设置
            $login = db('config')->where('inc_type','login')->select();
            $login_info = convert_arr_kv($login,'name','value');
            $this->assign('login', $login_info);
            $this->assign('title','会员登录');
            return $this->fetch();
        }
    }

    /**
     * 验证码图片
     * @return \think\Response
     */
    public function verify()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    25,
            // 验证码位数
            'length'      =>    4,
            // 关闭验证码杂点
            'useNoise'    =>    false,
            'bg'          =>    [255,255,255],
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    /**
     * 校验验证码是否正确
     * @param $code
     * @return bool
     */
    private function _check($code)
    {
        return captcha_check($code);
    }

    /**
     * 校验短信验证码
     * @param $code
     * @return bool
     */
    private function _checkSms($code)
    {
        /*var_dump(session('sms_code'));
        var_dump(session('sms_time'));die;*/
        if (session('sms_code') == $code && (session('sms_time')+60) > time())
        {
            return true;
        }
        return false;
    }

    /**
     * 注册
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function reg()
    {
        if (Request::isAjax()) {
            $data = input('post.');
            // 校验短信验证码
            if ($this->config['alisms_isopen'] == 'true')
            {
                $sms_code = $data['smscode'];
                if (!$this->_checkSms($sms_code)) {
                    return array('code' => 0, 'msg' => '短信验证码错误');
                }
            }
            $is_validated = 0 ;
            if (is_mobile_phone($data['mobile']))
            {
                $is_validated = 1;
                $data['mobile_validated'] = 1;
                if(get_user_info($data['mobile'],2)){
                    return array('code'=>-1,'msg'=>'手机账号已存在');
                }
            }
            if ($is_validated != 1)
            {
                return array('code'=>0,'msg'=>'请用手机号注册');
            }
            if (!$data['password'])
            {
                return array('code'=>-1,'msg'=>'请输入密码');
            }
            //验证两次密码是否匹配
            if ($data['password'] != $data['password2'])
            {
                return array('code'=>-1,'msg'=>'两次输入密码不一致');
            }
            unset($data['password2']);
            unset($data['vercode']);
            unset($data['smscode']);
            //验证是否存在用户名

            $data['password'] = md5($data['password']);
            $data['reg_time'] = time();
            $id = db('users')->insertGetId($data);
            if ($id === false)
            {
                return array('code'=>-1,'msg'=>'注册失败');
            }
            $user = db('users')->field('id,username')->where("id", $id)->find();
            session('user',$user);
            return array('code'=>1,'msg'=>'注册成功','result'=>$user);
        } else {
            // 登录设置
            $login = db('config')->where('inc_type','login')->select();
            $login_info = convert_arr_kv($login,'name','value');
            $this->assign('login', $login_info);
            $this->assign('title', '会员注册');
            return $this->fetch();
        }
    }

    /**
     * 找回密码
     * @return array|mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function forget()
    {
        if (request()->isPost())
        {
            $sender = input('mobile');
            $sms_code =input('smscode');
            $inValid = true;  //验证码失效
            if (!$sms_code)
            {
                return array('code'=>-1,'msg'=>'请输入手机验证码');
            }
            // 校验短信验证码
            if ($this->config['alisms_isopen'] == 'true')
            {
                if (!$this->_checkSms($sms_code)) {
                    return array('code' => 0, 'msg' => '短信验证码错误');
                }
            }
            $password = input('password');
            $password2 = input('password2');
            if ($password != $password2)
            {
                return array('code'=>-1,'msg'=>'两次输入密码不一致');
            }

            db('users')->where('mobile',$sender)->update(['password'=>md5($password)]);
            return array('code'=>1,'msg'=>'密码找回成功！');
        } else {
            $this->assign('title','找回密码');
            return $this->fetch();
        }
    }

    /**
     * 发送邮件验证码
     * @return array|\think\response\Json
     * @throws \Exception
     */
    public function sendEmail()
    {
        if (request()->isPost())
        {
            $sender = input('email');
            $sms_time_out = 180;
            //获取上一次的发送时间
            $send = session('validate_code');
            if (!empty($send) && $send['time'] > time() && $send['sender'] == $sender)
            {
                //在有效期范围内 相同号码不再发送
                return json(['code' => -1, 'msg' => '规定时间内,不要重复发送验证码']);
            }
            $code = mt_rand(1000, 9999);
            //检查是否邮箱格式
            if (!is_email($sender))
            {
                return json(['code' => -1, 'msg' => '邮箱码格式有误']);
            }
            $send = send_email($sender, '验证码', '您好，你的验证码是：' . $code);
            if ($send)
            {
                $info['code'] = $code;
                $info['sender'] = $sender;
                $info['is_check'] = 0;
                $info['time'] = time() + $sms_time_out; //有效验证时间
                session('validate_code', $info);
                return json(['code' => 1, 'msg' => '验证码已发送，请注意查收']);
            } else {
                return array('code' => -1, 'msg' => '验证码发送失败,请联系管理员');
            }
        } else {
            return json(['code' => -1, 'msg' => '非法请求']);
        }
    }

}