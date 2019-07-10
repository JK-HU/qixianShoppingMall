<?php
/**
*
* example目录下为简单的支付样例，仅能用于搭建快速体验微信支付使用
* 样例的作用仅限于指导如何使用sdk，在安全上面仅做了简单处理， 复制使用样例代码时请慎重
* 请勿直接直接使用样例对外提供服务
* 
**/
namespace wxpay\op;
use wxpay\lib\WxPayConfigInterface;

class WxPayConfig extends WxPayConfigInterface
{

    private $_config;

    public function __construct()
    {
        if (!cache('Config')) {
            savecache('Config');
        }
        $this->_config = cache('Config');
    }

    public function GetAppId()
	{
		return $this->_config['wxpay_appid'];
	}

	public function GetMerchantId()
	{
		return  $this->_config['wxpay_merchantid'];
	}

	public function GetNotifyUrl()
	{
		return "";
	}

	public function GetSignType()
	{
		return $this->_config['wxpay_signtype'];
	}

	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	public function GetProxy(&$proxyHost, &$proxyPort)
	{
		$proxyHost = "0.0.0.0";
		$proxyPort = 0;
	}

    /**
     * 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     * @return int
     */
	public function GetReportLevenl()
	{
		return 1;
	}

	public function GetKey()
	{
		return $this->_config['wxpay_appkey'];
	}

	public function GetAppSecret()
	{
		return $this->_config['wxpay_appsecret'];
	}

    /**
     * 设置商户证书路径
     * @param $sslCertPath
     * @param $sslKeyPath
     */
	public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
	{
		$sslCertPath = $this->_config['wxpay_sslcertpath'];
		$sslKeyPath = $this->_config['wxpay_sslkeypath'];
	}
}
