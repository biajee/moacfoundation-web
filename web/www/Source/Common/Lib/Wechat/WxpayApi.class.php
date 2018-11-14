<?php
require_once "Utils.class.php";
class WxpayApi {
    private $config;
    public function __construct($config) {
        $this->WxpayApi($config);
    }

    public function WxpayApi($config) {
        $this->config = $config;
    }

    public function makeSign($data) {
        $key = $this->config['KEY'];
        if (empty($key))
            throw new Exception("未设置key", 1);
        ksort($data);
        $str = Utils::toUrlParams($data);
        $str .= "&key=".$key;
        $str = md5($str);
        $result = strtoupper($str);
        return $result;
    }

    public function checkSign($data) {
        $sign = $this->makeSign($data);
        if ($data['sign'] != $sign)
            throw new Exception("签名错误", 1);

    }
    //发送xml
    function postXml($xml, $url, $useCert=false, $timeout=30) {
        $ch = curl_init();
        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //配置代理
        $proxyHost = $this->config['PROXY_HOST'];
        $proxyPort = $this->config['PROXY_PORT'];
        if (!empty($proxyHost) && $proxyHost != '0.0.0.0' && $proxyPort != 0 ) {
            curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        }
        //设置header
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($useCert) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            $certPath = $this->config['SSLCERT_PATH'];
            $keyPath = $this->config['SSLKEY_PATH'];
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $certPath);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $keyPath);
        }
        //post提交
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new Exception("提交出错，错误码：$error");
        }
    }

    //处理返回结构
    public function getResult($response) {
        $result = Utils::fromXML($response);
        if ($result['return_code'] != 'SUCCESS')
            return $result;
        $this->checkSign($result);
        return $result;
    }

    /**
     *
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param  $order
     * @param int $timeOut
     * @throws Exception
     * @return 成功时返回，其他抛异常
     */
    public function unifiedOrder($order, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if(empty($order['out_trade_no'])) {
            throw new Exception("缺少统一支付接口必填参数out_trade_no！");
        }else if(empty($order['body'])) {
            throw new Exception("缺少统一支付接口必填参数body！");
        }else if(empty($order['total_fee'])) {
            throw new Exception("缺少统一支付接口必填参数total_fee！");
        }else if(empty($order['trade_type'])) {
            throw new Exception("缺少统一支付接口必填参数trade_type！");
        }

        //关联参数
        if($order['trade_type'] == "JSAPI" && empty($order['openid'])){
            throw new Exception("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if($order['trade_type'] == "NATIVE" && empty($order['product_id'])){
            throw new Exception("统一支付接口中，缺少必填参数product_id！trade_type为NATIVE时，product_id为必填参数！");
        }
        $noifyUrl = $this->config['NOTIFY_URL'];
        $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        //异步通知url未设置，则使用配置文件中的url
        if(empty($order['notify_url'])){
            $order['notify_url'] = $noifyUrl;//异步通知url
        }

        $order['appid'] = $appid;//公众账号ID
        $order['mch_id'] = $mchId;//商户号
        $order['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];//终端ip
        $order['nonce_str'] = Utils::getNonceStr();//随机字符串

        //签名
        $sign = $this->makeSign($order);
        $order['sign'] = $sign;
        $xml = Utils::ToXml($order);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间

        $response = $this->postXml($xml, $url, false, $timeOut);
        Utils::log($response, 'unifiedOrder');
        $result = $this->getResult($response);
        //$this->reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        return $result;
    }

    /**
     *
     * 撤销订单API接口，参数out_trade_no和transaction_id必须填写一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayReverse $params
     * @param int $timeOut
     * @throws Exception
     */
    public function reverse($params, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";
        //检测必填参数
        if(empty($params['out_trade_no']) && empty($params['transaction_id'])) {
            throw new Exception("撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！");
        }
        //完善信息
        $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        $order['appid'] = $appid;//公众账号ID
        $order['mch_id'] = $mchId;//商户号
        $order['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];//终端ip
        $order['nonce_str'] = Utils::getNonceStr();//随机字符串

        //签名
        $sign = $this->makeSign($order);
        $order['sign'] = $sign;
        $xml = Utils::ToXml($order);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间
 
        $response = $this->postXml($xml, $url, false, $timeOut);
        $result = $this->getResult($response);
        //$this->reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
    }

    /**
     *
     * 查询订单，$params中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayOrderQuery $params
     * @param int $timeOut
     * @throws Exception
     * @return 成功时返回，其他抛异常
     */
    public function queryOrder($params, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if(empty($params['out_trade_no']) && empty($params['transaction_id'])) {
            throw new Exception("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        $params['appid'] = $appid;//公众账号ID
        $params['mch_id'] = $mchId;//商户号
        $params['nonce_str'] = Utils::getNonceStr();//随机字符串

        $sign = $this->makeSign($params);
        $params['sign'] = $sign;
        $xml = Utils::ToXml($params);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间
        $response = $this->postXml($xml, $url, false, $timeOut);
        Utils::log($response, 'queryOrder');
        $result = $this->getResult($response);
        //$this->reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        return $result;
    }

    /**
     *
     * 关闭订单，out_trade_no必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param $params
     * @param int $timeOut
     * @throws Exception
     * @return 成功时返回，其他抛异常
     */
    public function closeOrder($params, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        //检测必填参数
        if(empty($params['out_trade_no'])) {
            throw new Exception("订单查询接口中，out_trade_no必填！");
        }
        $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        $params['appid'] = $appid;//公众账号ID
        $params['mch_id'] = $mchId;//商户号
        $params['nonce_str'] = Utils::getNonceStr();//随机字符串

        $sign = $this->makeSign($params);
        $params['sign'] = $sign;
        $xml = Utils::ToXml($params);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间
        $response = $this->postXml($xml, $url, false, $timeOut);
        $result = $this->getResult($response);
        //$this->reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 申请退款，out_trade_no、transaction_id至少填一个且
     * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param $params
     * @param int $timeOut
     * @throws Exception
     * @return 成功时返回，其他抛异常
     */
    public function refund($params, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        //检测必填参数
        if(empty($params['out_trade_no']) && empty($params['transaction_id'])) {
            throw new Exception("退款申请接口中，out_trade_no、transaction_id至少填一个！");
        }else if(empty($params['out_refund_no'])){
            throw new Exception("退款申请接口中，缺少必填参数out_refund_no！");
        }else if(empty($params['total_fee'])){
            throw new Exception("退款申请接口中，缺少必填参数total_fee！");
        }else if(empty($params['refund_fee'])){
            throw new Exception("退款申请接口中，缺少必填参数refund_fee！");
        }else if(empty($params['op_user_id'])){
            throw new Exception("退款申请接口中，缺少必填参数op_user_id！");
        }

        $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        $params['appid'] = $appid;//公众账号ID
        $params['mch_id'] = $mchId;//商户号
        $params['nonce_str'] = Utils::getNonceStr();//随机字符串

        $sign = $this->makeSign($params);
        $params['sign'] = $sign;
        $xml = Utils::ToXml($params);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间
        $response = $this->postXml($xml, $url, true, $timeOut);
        $result = $this->getResult($response);
        //$this->reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     *
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param $params
     * @param int $timeOut
     * @throws Exception
     * @return 成功时返回，其他抛异常
     */
    public function queryRefund($params, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //检测必填参数
        if(empty($params['out_refund_no']) &&
            empty($params['out_trade_no']) &&
            empty($params['transaction_id']) &&
           empty($params['refund_id'])) {
            throw new Exception("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }

       $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        $params['appid'] = $appid;//公众账号ID
        $params['mch_id'] = $mchId;//商户号
        $params['nonce_str'] = Utils::getNonceStr();//随机字符串

        $sign = $this->makeSign($params);
        $params['sign'] = $sign;
        $xml = Utils::ToXml($params);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间
        $response = $this->postXml($xml, $url, false, $timeOut);
        $result = $this->getResult($response);
        //$this->reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }

    /**
     * 下载对账单，bill_date为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param $params
     * @param int $timeOut
     * @throws Exception
     * @return 成功时返回，其他抛异常
     */
    public function downloadBill($params, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        //检测必填参数
        if(empty($params['bill_date'])) {
            throw new Exception("对账单接口中，缺少必填参数bill_date！");
        }
        $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        $params['appid'] = $appid;//公众账号ID
        $params['mch_id'] = $mchId;//商户号
        $params['nonce_str'] = Utils::getNonceStr();//随机字符串

        $sign = $this->makeSign($params);
        $params['sign'] = $sign;
        $xml = Utils::ToXml($params);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间
        $response = $this->postXml($xml, $url, false, $timeOut);
        if(substr($response, 0 , 5) == "<xml>"){
            return "";
        }
        return $response;
    }
    /**
     *
     * 支付结果通用通知
     * @param function $callback
     * 直接回调函数使用方法: notify(you_function);
     * 回调类成员函数方法:notify(array($this, you_function));
     * $callback  原型为：function function_name($data){}
     */
    public function notify($callback, &$msg)
    {
        //获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        Utils::log($xml,'notify');
        //如果返回成功则验证签名
        try {
            $result = Utils::fromXML($xml);
        } catch (Exception $e){
            $msg = $e->errorMessage();
            return false;
        }

        return call_user_func($callback, $result);
    }

    /**
     * 直接输出xml
     * @param string $xml
     */
    public function replyNotify($data, $needSign=true)
    {
        if ($needSign) {
            $data['sign'] = $this->makeSign($data);
        }
        $xml = Utils::toXML($data);
        echo $xml;
    }

    /**
     *
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayReport $params
     * @param int $timeOut
     * @throws Exception
     * @return 成功时返回，其他抛异常
     */
    public function report($data, $timeOut = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if(empty($data['interface_url'])) {
            throw new Exception("接口URL，缺少必填参数interface_url！");
        } if(empty($data['return_code'])) {
            throw new Exception("返回状态码，缺少必填参数return_code！");
        } if(empty($data['user_ip'])) {
            throw new Exception("业务结果，缺少必填参数result_code！");
        } if(empty($data['interface_url'])) {
            throw new Exception("访问接口IP，缺少必填参数user_ip！");
        } if(empty($data['execute_time_'])) {
            throw new Exception("接口耗时，缺少必填参数execute_time_！");
        }
        $appid = $this->config['APPID'];
        $mchId = $this->config['MCHID'];
        $data['appid'] = $appid;//公众账号ID
        $data['mch_id'] = $mchId;//商户号
        $data['user_id'] = $_SERVER['REMOTE_ADDR'];//终端ip
        $data['time'] = date("YmdHis");//商户上报时间
        $data['nonce_str'] = Utils::getNonceStr();//随机字符串

        $sign = $this->makeSign($data);
        $data['sign'] = $sign;
        $xml = Utils::ToXml($data);

        $startTimeStamp = Utils::getMillisecond();//请求开始时间
        $response = $this->postXml($xml, $url, false, $timeOut);
        return $response;
    }

    /**
     *
     * 上报数据， 上报的时候将屏蔽所有异常流程
     * @param string $usrl
     * @param int $startTimeStamp
     * @param array $data
     */
    private function reportCostTime($url, $startTimeStamp, $data)
    {
        $levenl = $this->config['REPORT_LEVENL'];
        //如果不需要上报数据
        if($levenl == 0){
            return;
        }
        //如果仅失败上报
        if($levenl == 1 &&
             array_key_exists("return_code", $data) &&
             $data["return_code"] == "SUCCESS" &&
             array_key_exists("result_code", $data) &&
             $data["result_code"] == "SUCCESS")
         {
            return;
         }

        //上报逻辑
        $endTimeStamp = Utils::getMillisecond();
        $params = array();
        $params['interface_url'] = $url;
        $params['execute_time_'] = $endTimeStamp - $startTimeStamp;
        //返回状态码
        if(array_key_exists("return_code", $data)){
            $params['return_code'] = $data["return_code"];
        }
        //返回信息
        if(array_key_exists("return_msg", $data)){
            $params['return_msg'] = $data["return_msg"];
        }
        //业务结果
        if(array_key_exists("result_code", $data)){
            $params['result_code'] = $data["result_code"];
        }
        //错误代码
        if(array_key_exists("err_code", $data)){
            $params['err_code'] = $data["err_code"];
        }
        //错误代码描述
        if(array_key_exists("err_code_des", $data)){
            $params['err_code_des'] = $data["err_code_des"];
        }
        //商户订单号
        if(array_key_exists("out_trade_no", $data)){
            $params['out_trade_no'] = $data["out_trade_no"];
        }
        //设备号
        if(array_key_exists("device_info", $data)){
            $params['device_info'] = $data["device_info"];
        }

        try{
            $this->report($params);
        } catch (Exception $e){
            //不做任何处理
        }
    }
    /**
     *
     * 获取jsapi支付的参数
     * @param array $order 统一支付接口返回的数据
     * @throws Exception
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function getJsApiParameters($order)
    {
        if(!array_key_exists("appid", $order)
        || !array_key_exists("prepay_id", $order)
        || $order['prepay_id'] == "")
        {
            throw new Exception("参数错误");
        }
        $params = array();
        $params['appId'] = $order["appid"];
        $timeStamp = time();
        $params['timeStamp'] = "$timeStamp";
        $params['nonceStr'] = Utils::getNonceStr();
        $params['package'] = 'prepay_id=' . $order['prepay_id'];
        $params['signType'] = "MD5";
        $params['paySign'] = $this->makeSign($params);
        $result = json_encode($params);
        return $result;
    }

}
