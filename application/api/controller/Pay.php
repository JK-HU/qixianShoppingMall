<?php
/**
 * 支付接口
 */
namespace app\api\controller;
use alipay\AlipayConfig;
use alipay\wappay\buildermodel\AlipayTradeWapPayContentBuilder;
use alipay\wappay\service\AlipayTradeService;
use alipay\wappay\buildermodel\AlipayTradeQueryContentBuilder;
use app\common\controller\Common;
use app\common\model\UserIntegralLog;
use app\user\model\Users;
use app\user\model\UserVip;
use app\common\model\ShopOrder;
use app\user\model\UserVipOrder;
use Exception;
use http\Client\Curl\User;
use QRcode;
use think\facade\Env;
use wxpay\lib\WxPayApi;
use wxpay\lib\WxPayOrderQuery;
use wxpay\lib\WxPayUnifiedOrder;
use wxpay\op\JsApiPay;
use wxpay\op\NativePay;
use wxpay\op\WxPayConfig;

class Pay extends Common
{
    private $_wxpay_wap_name;
    private $_wxpay_notify_url;
    private $_wxpay_scene_info;

    public function initialize()
    {
        parent::initialize();
        $this->_wxpay_wap_name = '务团网';
        $this->_wxpay_notify_url = url('api/pay/wxpayNotify', [], true, true);
        $this->_wxpay_scene_info = [
            "h5_info" => [
                "type" => "Wap",
                "wap_url" => "https://pay.qq.com",
                "wap_name" => $this->_wxpay_wap_name
            ]
        ];
    }

    /**
     * 微信支付
     * @param $type
     * @param $id
     * @param string $the_type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wxpay($type, $id, $the_type='shop')
    {
        if ($id && $type) {
            $order_info = $this->_get_order_info($the_type, $id);
            $return_url = url('api/pay/wxpayOrderQuery', ['type'=>$type, 'id'=>$id, 'the_type'=>$the_type], true, true);
            if ($order_info) {
                $pay_data = [
                    'fee' => $order_info['money'],
                    'body' => $order_info['name'],
                    'out_trade_no' => $order_info['order_id'],
                    'attach' => $id,
                    'return_url' => $return_url
                ];
                $type = ucwords($type);
                $function = '_wxpay'.$type;
                $this->$function($pay_data);
            } else {
                echo '非法操作';
            }
        }
    }

    /**
     * 微信订单查询
     * @param $type
     * @param $id
     * @param string $the_type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wxpayOrderQuery($type, $id, $the_type='shop')
    {
        if ($id && $type) {
            $order_info = $this->_get_order_info($the_type, $id);
            if ($order_info) {
                $data = [
                    'out_trade_no' => $order_info['order_id'],
                ];
                $this->_wxpayOrderQuery($data);
            } else {
                echo '非法操作';
            }
        }
    }

    /**
     * 阿里云支付
     * @param $type
     * @param $id
     * @param string $the_type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function alipay($type, $id, $the_type='shop')
    {
        if ($id && $type) {
            $order_info = $this->_get_order_info($the_type, $id);
            $return_url = url('api/pay/alipayOrderQuery', ['type'=>$type, 'id'=>$id, 'the_type'=>$the_type], true, true);
            if ($order_info) {
                $pay_data = [
                    'fee' => $order_info['money'],
                    'body' => $order_info['name'],
                    'out_trade_no' => $order_info['order_id'],
                    'subject' => $order_info['name'],
                    'return_url' => $return_url
                ];
                $type = ucwords($type);
                $function = '_alipay'.$type;
                $this->$function($pay_data);
            } else {
                echo '支付非法操作';
            }
        }
    }

    /**
     * 支付宝订单查询
     * @param $type
     * @param $id
     * @param string $the_type
     * @throws Exception
     */
    public function alipayOrderQuery($type, $id, $the_type='shop')
    {
        if ($id && $type) {
            $order_info = $this->_get_order_info($the_type, $id, false, 2);
            if ($order_info && $order_info['order_id'] == input('get.out_trade_no')) {
                $data = [
                    'out_trade_no' => $order_info['order_id'],
                ];
                $this->_alipayOrderQuery($data);
            } else {
                echo '查询非法操作';
                var_export($the_type);
                var_export($id);
                var_export($order_info);
                var_export(input('get.out_trade_no'));
            }
        }
    }

    /**
     * 微信支付 - 扫码支付方式二： 统一下单，生成二维码
     * @param $data
     * @return string
     * @throws \wxpay\lib\WxPayException
     */
    private function _wxpayNativeTwo($data)
    {
        if ($data)
        {
            // 统一下单参数
            $true_fee = $data['fee']*100;
            $notify = new NativePay();
            $input = new WxPayUnifiedOrder();
            $input->SetBody($data['body']);
            $input->SetTotal_fee($true_fee);
            $input->SetOut_trade_no($data['out_trade_no']);
            $input->SetTrade_type("NATIVE");
            isset($data['device_info']) && $input->SetAttach($data['device_info']);
            isset($data['attach']) && $input->SetAttach($data['attach']);
            isset($data['fee_type']) && $input->SetFee_type($data['fee_type']);
            isset($data['time_start']) && $input->SetTime_start($data['time_start']);
            isset($data['time_expire']) && $input->SetTime_expire($data['time_expire']);
            isset($data['goods_tag']) && $input->SetGoods_tag($data['goods_tag']);
            isset($data['product_id']) && $input->SetProduct_id($data['product_id']);
            isset($data['limit_pay']) && $input->SetLimit_pay($data['limit_pay']);
            isset($data['openid']) && $input->SetOpenid($data['openid']);
            isset($data['receipt']) && $input->SetReceipt($data['receipt']);
            isset($data['scene_info']) && $input->SetScene_info($data['scene_info']);
            isset($data['notify_url']) ? $input->SetNotify_url($data['notify_url']) : $input->SetNotify_url($this->_wxpay_notify_url);
            // 获取生成直接支付url
            $result = $notify->GetPayUrl($input);
            $url = $result["code_url"];
            // 校验Url以及生成返回二维码图片并返回路径
            if(substr($url, 0, 6) == "weixin")
            {
                $re_img = '/data/qrcode/'.time().'.png';
                $true_re_img = Env::get('root_path').'public'.$re_img;
                QRcode::png($url, $true_re_img);
                return $re_img;
            } else {
                header('HTTP/1.1 404 Not Found');
            }
        }
    }

    /**
     * 微信支付 - JSAPI支付
     * @param $data
     * @throws \wxpay\lib\WxPayException
     */
    private function _wxpayJsApi($data)
    {
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        // 统一下单
        $true_fee = $data['fee']*100;
        $input = new WxPayUnifiedOrder();
        $input->SetBody($data['body']);
        $input->SetTotal_fee($true_fee);
        $input->SetOut_trade_no($data['out_trade_no']);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        isset($data['device_info']) && $input->SetAttach($data['device_info']);
        isset($data['attach']) && $input->SetAttach($data['attach']);
        isset($data['fee_type']) && $input->SetFee_type($data['fee_type']);
        isset($data['time_start']) && $input->SetTime_start($data['time_start']);
        isset($data['time_expire']) && $input->SetTime_expire($data['time_expire']);
        isset($data['goods_tag']) && $input->SetGoods_tag($data['goods_tag']);
        isset($data['product_id']) && $input->SetProduct_id($data['product_id']);
        isset($data['limit_pay']) && $input->SetLimit_pay($data['limit_pay']);
        isset($data['receipt']) && $input->SetReceipt($data['receipt']);
        isset($data['scene_info']) && $input->SetScene_info($data['scene_info']);
        isset($data['notify_url']) ? $input->SetNotify_url($data['notify_url']) : $input->SetNotify_url($this->_wxpay_notify_url);
        $config = new WxPayConfig();
        $order = WxPayApi::unifiedOrder($config, $input);
        // echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        // printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        // 获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();

        $this->assign('fee', $true_fee);
        $this->assign('jsApiParameters', $jsApiParameters);
        $this->assign('editAddress', $editAddress);
    }

    /**
     * 微信支付 - H5支付
     * @param $data
     * @throws \wxpay\lib\WxPayException
     */
    private function _wxpayH5($data)
    {
        // 统一下单
        $true_fee = $data['fee']*100;
        $input = new WxPayUnifiedOrder();
        $input->SetBody($data['body']);
        $input->SetTotal_fee($true_fee);
        $input->SetOut_trade_no($data['out_trade_no']);
        $input->SetTrade_type("MWEB");
        $input->SetScene_info($data['scene_info']);
        isset($data['device_info']) && $input->SetAttach($data['device_info']);
        isset($data['attach']) && $input->SetAttach($data['attach']);
        isset($data['fee_type']) && $input->SetFee_type($data['fee_type']);
        isset($data['time_start']) && $input->SetTime_start($data['time_start']);
        isset($data['time_expire']) && $input->SetTime_expire($data['time_expire']);
        isset($data['goods_tag']) && $input->SetGoods_tag($data['goods_tag']);
        isset($data['product_id']) && $input->SetProduct_id($data['product_id']);
        isset($data['limit_pay']) && $input->SetLimit_pay($data['limit_pay']);
        isset($data['receipt']) && $input->SetReceipt($data['receipt']);
        isset($data['notify_url']) ? $input->SetNotify_url($data['notify_url']) : $input->SetNotify_url($this->_wxpay_notify_url);
        $config = new WxPayConfig();
        $order = WxPayApi::unifiedOrder($config, $input);
        $this->redirect($order['mweb_url']);
        // echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        // $this->_printf_info($order);
    }

    /**
     * 刷卡支付
     * @param $data
     */
    private function _wxpayMicropay($data)
    {

    }

    /**
     * 微信支付 - 订单查询
     * @param $data
     */
    private function _wxpayOrderQuery($data)
    {
        $transaction_id = $data['transaction_id'];
        $out_trade_no = $data['out_trade_no'];
        if ($transaction_id != "" && !preg_match("/^[0-9a-zA-Z]{10,64}$/i", $transaction_id, $matches) || $out_trade_no != "" && !preg_match("/^[0-9a-zA-Z]{10,64}$/i", $out_trade_no, $matches)) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }

        // 微信订单号
        if( $transaction_id != "" )
        {
            try {
                $input = new WxPayOrderQuery();
                $input->SetTransaction_id($transaction_id);
                $config = new WxPayConfig();
                printf_info(WxPayApi::orderQuery($config, $input));
            } catch(Exception $e) {
                $this->_wxpay_query_order_log(var_export(json_encode($e)));
            }
            exit();
        }

        // 商户订单号
        if(isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != "")
        {
            try{
                $out_trade_no = $_REQUEST["out_trade_no"];
                $input = new WxPayOrderQuery();
                $input->SetOut_trade_no($out_trade_no);
                $config = new WxPayConfig();
                $this->_printf_info(WxPayApi::orderQuery($config, $input));
            } catch(Exception $e) {
                $this->_wxpay_query_order_log(var_export(json_encode($e)));
            }
            exit();
        }
    }

    /**
     * 下载对账单
     * @param $data
     */
    private function _wxpayDownload($data)
    {

    }

    /**
     * 退款操作
     * @param $data
     */
    private function _wxpayRefund($data)
    {

    }

    /**
     * 退款查询
     * @param $data
     */
    private function _wxpayRefundQuery($data)
    {

    }

    /**
     * 打印信息
     * @param $data
     */
    private function _printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#f00;'>$key</font> : ".htmlspecialchars($value, ENT_QUOTES)." <br/>";
        }
    }

    /**
     * 微信支付 - 回调方法
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wxpayNotify()
    {
        $user_vip_order = new UserVipOrder();
        $result = true;

        $this->_wxpay_notify_log('------ 接收回调开始 ------');
        $xml = file_get_contents("php://input");
        $this->_wxpay_notify_log('接收XML:'.PHP_EOL.$xml);

        if(!$xml){
            $this->_wxpay_notify_log('xml数据异常');
            return false;
        }

        // 解析数据
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $this->_wxpay_notify_log('处理后XML:'.PHP_EOL.var_export($data,true));

        // 检查订单类型
        if (mb_substr($data['out_trade_no'], 0, 6) == 'member') {
            $info = $user_vip_order->where(['order_id'=>$data['out_trade_no']])->find();
        } else {
            $info = ShopOrder::where(['order_id'=>$data['out_trade_no']])->find();
        }

        // 检查订单是否存在
        if (!$info) {
            $result = false;
            $this->_wxpay_notify_log('订单不存在');
        }

        // 检查金额是否一致
        $money = $data['total_fee']/100;
        if ($money != $info['money']) {
            $result = false;
            $this->_wxpay_notify_log('');
        }

        // 进行参数校验检测到金额不一致
        if(!array_key_exists("openid", $data) && !array_key_exists("product_id", $data))
        {
            $result = false;
            $this->_wxpay_notify_log('回调数据异常');
        }

        // 进行签名验证
        $sign = $this->_wxpay_check_sign($data);
        if($data['sign'] != $sign) {
            $result = false;
            $this->_wxpay_notify_log('签名错误');
        }

        // 验证成功
        if ($result) {
            $this->_after_pay_action($info, $data);
        }

        $this->_wxpay_notify_log('------ 接收回调并处理结束 ------');

        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

    /**
     * 支付宝手机网站支付
     * @param $data
     * @throws Exception
     */
    private function _alipayWap($data)
    {
        if ( !empty($data['out_trade_no']) && trim($data['out_trade_no'])!="" )
        {
            $config = new AlipayConfig();
            // 超时时间
            $timeout_express="1m";

            $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setOutTradeNo($data['out_trade_no']);
            $payRequestBuilder->setSubject($data['subject']);
            $payRequestBuilder->setTotalAmount($data['fee']);
            $payRequestBuilder->setTimeExpress($timeout_express);
            isset($data['body']) && $payRequestBuilder->setBody($data['body']);
            isset($data['return_url']) && $config->return_url = $data['return_url'];
            isset($data['notify_url']) && $config->notify_url = $data['notify_url'];
            // var_export($config);die;
            $payResponse = new AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder, $config->return_url, $config->notify_url);
            return ;
        }
    }

    /**
     * 支付宝回调方法
     * @throws Exception
     */
    public function alipayNotify()
    {
        $arr = $_POST;
        $config = new AlipayConfig();
        $user_vip_order = new UserVipOrder();
        $shop_order = new ShopOrder();
        $alipaySevice = new AlipayTradeService($config);
        // $alipaySevice->writeLog(var_export(http_build_query($_POST),true));
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);
        $alipaySevice->writeLog(var_export($result,true));
        // 检查订单类型
        if (mb_substr($arr['out_trade_no'], 0, 6) == 'member') {
            $info = $user_vip_order->where(['order_id'=>$arr['out_trade_no']])->find();
        } else {
            $info = $shop_order->where(['order_id'=>$arr['out_trade_no']])->find();
        }
        // 检查订单是否存在
        if (!$info) {
            $result = false;
            $alipaySevice->writeLog('订单不存在');
        }
        // 检查金额是否一致
        if ($arr['total_amount'] != $info['money']) {
            $result = false;
            $alipaySevice->writeLog('检测到金额不一致');
        }
        // 验证appid是否一致
        if ($arr['app_id'] != $config->app_id) {
            $result = false;
            $alipaySevice->writeLog('验证appid是否一致2');
        }
        // 验证成功
        if ($result) {
            // 交易结束，不可退款
            if ($arr['trade_status'] == 'TRADE_FINISHED')
            {
                $alipaySevice->writeLog('交易结束，不可退款');
                // 注意：
                // 退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }

            // 交易支付成功
            if ($arr['trade_status'] == 'TRADE_SUCCESS')
            {
                $alipaySevice->writeLog('交易支付成功');
                $this->_after_pay_action($info, $arr);
                // 注意：
                // 付款完成后，支付宝系统发送该交易状态通知
            }

            echo "success";		//请不要修改或删除

        } else {
            //验证失败
            echo "fail";	//请不要修改或删除
        }
    }

    /**
     * 支付宝订单查询
     * @param $data
     * @throws Exception
     */
    private function _alipayOrderQuery($data)
    {
        //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
        if (!empty($data['out_trade_no']) || !empty($data['trade_no']))
        {
            //支付宝交易号，和商户订单号二选一
            $out_trade_no = trim($data['out_trade_no']);
            $trade_no = trim($data['trade_no']);

            $RequestBuilder = new AlipayTradeQueryContentBuilder();
            !empty($trade_no) && $RequestBuilder->setTradeNo($trade_no);
            !empty($out_trade_no) && $RequestBuilder->setOutTradeNo($out_trade_no);

            $config = new AlipayConfig();
            $Response = new AlipayTradeService($config);
            $result = $Response->Query($RequestBuilder);

            if ($result->trade_status == 'TRADE_SUCCESS') {
                $url = url('home/order/success', ['status'=>'success']);
            } else {
                $url = url('home/order/success', ['status'=>'fail']);
            }
            $this->redirect($url);
        }
    }

    /**
     * 支付成功后进行的操作
     * @param $info
     * @param $arr
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _after_pay_action($info, $arr)
    {
        $is_member = (mb_substr($arr['out_trade_no'], 0, 6) == 'member') ? true : false;
        // 检查当前状态，该订单是否处理过,未处理则处理
        if ($info['status'] == 1) {
            // 发送短信通知给卖家
            $this->_sendSellerSms($info);
            // 修改订单状态
            $info->save(['status'=>2]);
            // 如果是会员，则增加或者延长会员时间
            if ($is_member) {
                $this->_vip_recharge($info);
            } else { // 如果购买的是商品，则根据系统设置积分金额比例，赠送积分
                $this->_give_integral($info);
            }
        }
    }

    /**
     * 发送卖家通知短信
     * @param $info
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _sendSellerSms($info)
    {
        $user = db('users')->field('mobile')->find($info['uid']);
        $data = [
            'username' => $user['mobile'],
            'shopname' => $info['name'],
            'money' => $info['money']
        ];
        $sd = action('api/sms/sendAlismsNotification', ['info'=>$data]);
        error_log(var_export(11, true), 3, 'pay_sms.txt');
        error_log(var_export($sd, true), 3, 'pay_sms.txt');
    }

    /**
     * 用户支付成功后，对于会员充值的一些操作
     * @param $info
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _vip_recharge($info)
    {
        $is_delete = false;
        $user_vip = UserVip::where(['uid'=>$info['uid']])->find();
        if ($user_vip) {
            // 如果目前是会员，没有过期，则延长
            if ( $user_vip['expires_time'] > time()) {
                $have_time = $user_vip['expires_time'] - time();
                $expires_time = strtotime('+1 month') + $have_time;
                $user_vip->save([
                    'expires_time' => $expires_time
                ]);
            } else { // 如果已过期了，则删除，重新创建会员
                $user_vip->delete();
                $is_delete = true;
            }
        }
        // 如果目前不是会员或者会员已经删除，创建会员
        if (!$user_vip or $is_delete) {
            $new_data = [
                'uid' => $info['uid'],
                'create_time' => time(),
                'expires_time' => strtotime('+1 month')
            ];
            db('user_vip')->insert($new_data);
        }
    }

    /**
     * 购买商品，赠送积分
     * @param $info
     * @throws \think\Exception
     * @throws \think\Exception\DbException
     */
    private function _give_integral($info)
    {
        $user_integral_log = new UserIntegralLog();
        $config = cache('Config');
        $give_integral = $config['give_integral'];
        $give_money = $info['money']*$give_integral;
        if ($give_money > 0) {
            $user = Users::get($info['uid']);
            if ($user) {
                $result = $user->setInc('integral', $give_money);
                if ($result) {
                    $user_integral_log->save([
                        'number' => $give_money,
                        'type' => 2,
                        'uid' => $info['uid'],
                        'reason' => '购买商品获得',
                        'create_time' => time()
                    ]);
                }
            }
        }
    }

    /**
     * 按规则返回签名字符串
     * @param $data
     * @return string
     */
    private function _wxpay_check_sign($data)
    {
        $config = new WxPayConfig();
        ksort($data);
        $string = $this->_wxpay_to_url_params($data);
        $string = $string . "&key=".$config->GetKey();
        if(strlen($data['sign']) <= 32) {
            $string = md5($string);
        } else {
            $string = hash_hmac("sha256",$string ,$config->GetKey());
        }
        $sign = strtoupper($string);
        return $sign;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $values
     * @return string
     */
    private function _wxpay_to_url_params($values)
    {
        $buff = "";
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 微信支付 - 记录回调日志
     * @param $content
     */
    private function _wxpay_notify_log($content)
    {
        $this->_log($content, 'wxpay_notify.log');
    }

    /**
     * 微信支付 - 记录查询订单日志
     * @param $content
     */
    private function _wxpay_query_order_log($content)
    {
        $this->_log($content, 'wxpay_query_order.log');
    }

    /**
     * 记录日志内容
     * @param $content
     * @param $file
     */
    private function _log($content, $file)
    {
        $content = date('Y-m-d H:i:s').'：'.PHP_EOL.$content.PHP_EOL;
        error_log($content,3, $file);
    }

    /**
     * 查询订单信息
     * @param $the_type
     * @param $id
     * @param bool $is_login
     * @param int $status
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function _get_order_info($the_type, $id, $is_login=true, $status=1)
    {
        $order_info = [];
        $main = '';
        if ( $the_type == 'shop' ) {
            $main = db('shop_order');
        }
        if ( $the_type == 'member' ) {
            $main = db('user_vip_order');
        }
        if ($is_login) {
            $order_info = $main->where(['status'=>$status, 'uid'=>session('user.id')])->find($id);
        } else {
            $order_info = $main->where(['status'=>$status])->find($id);
        }

        return $order_info;
    }
}