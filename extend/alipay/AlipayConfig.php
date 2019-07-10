<?php
namespace alipay;
/**
 * 配置文件
 * Class Config
 */
class AlipayConfig
{

    // 应用ID,您的APPID。
    private $_app_id;

    // 商户私钥，您的原始格式RSA私钥
    private $_merchant_private_key;

    // 异步通知地址
    private $_notify_url;

    // 同步跳转
    private $_return_url = '';

    // 编码格式
    private $_charset = "UTF-8";

    // 签名方式
    private $_sign_type = "RSA2";

    // 支付宝网关
    private $_gatewayUrl = "https://openapi.alipay.com/gateway.do";

    // 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    private $_alipay_public_key;

    public function __construct()
    {
        if (!cache('Config')) {
            savecache('Config');
        }
        $config = cache('Config');
        $this->_app_id = $config['alipay_appid'];
        $this->_merchant_private_key = $config['alipay_private_key'];
        $this->_alipay_public_key = $config['alipay_public_key'];
        $this->_return_url = url('user/index/index', [], true, true);
        $this->_notify_url = url('api/pay/alipayNotify', [], true, true);
    }

    public function __get($name)
    {
        $name = '_'.$name;
        return $this->$name;
    }

    public function __set($name, $value)
    {
        if ($name == 'return_url' || $name == 'notify_url') {
            $this->_return_url = $value;
        }
    }

}