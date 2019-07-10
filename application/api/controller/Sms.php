<?php
namespace app\api\controller;
use alisms\Client;
use alisms\Request\SendSms;
use app\common\controller\Common;
use think\facade\Request;

/**
 * 短信服务
 */
class Sms extends Common
{
    /**
     * 阿里云短信发送函数
     * @param string $params 短信信息结构
     * @$params['mobile'] 收件人手机号码
     * @$params['SmsTemplateCode']短信模版ID
     * @return boolean
     */
    public function sendAlisms()
    {
        if (Request::isAjax()) {
            $params = Request::post();
            if($this->config['alisms_isopen'] == 'true')
            {
                $config = [
                    'accessKeyId'     => $this->config['alisms_accesskeyid'],
                    'accessKeySecret' => $this->config['alisms_accesskeysecret'],
                ];
                $client  = new Client($config);
                $sendSms = new SendSms;
                $sendSms->setPhoneNumbers($params['mobile']);
                $sendSms->setSignName($this->config['alisms_sign']);
                $sendSms->setTemplateCode($params['SmsTemplateCode']);
                $templateParam = [
                    'code' =>   mt_rand(100000,999999)
                ];
                // 可选，设置模板参数
                if (!empty($templateParam))
                {
                    //模版参数为数组格式
                    $sendSms->setTemplateParam($templateParam);
                }
                // 可选，设置流水号
                if (!empty($params['outId']))
                {
                    $sendSms->setOutId($params['outId']);
                }
                $result = $client->execute($sendSms);
                if($result->Code=='OK')
                {
                    // 设置验证码session
                    session('sms_code', $templateParam['code']);
                    session('sms_time', time());
                    return true;
                } else{
                    // var_export($result);die;
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 卖家付款后发送消息通知
     * @param $info
     * @return bool
     */
    public function sendAlismsNotification($info)
    {
        if($this->config['alisms_isopen'] == 'true')
        {
            $config = [
                'accessKeyId'     => $this->config['alisms_accesskeyid'],
                'accessKeySecret' => $this->config['alisms_accesskeysecret'],
            ];
            $client  = new Client($config);
            $sendSms = new SendSms;
            $sendSms->setPhoneNumbers($this->config['seller_mobile']);
            $sendSms->setSignName('猎狼企业');
            $sendSms->setTemplateCode('SMS_167974992');
            $templateParam = [
                'username' =>   $info['username'],
                'shopname' =>   $info['shopname'],
                'money' =>   $info['money'],
                'time' =>   date('Y-m-d H:i:s'),
            ];
            // 可选，设置模板参数
            if (!empty($templateParam))
            {
                //模版参数为数组格式
                $sendSms->setTemplateParam($templateParam);
            }
            $result = $client->execute($sendSms);
            error_log(var_export($result, true), 3, 'pay_sms.txt');
            if($result->Code=='OK')
            {
                return true;
            } else{
                return false;
            }
        } else {
            return false;
        }
    }
}