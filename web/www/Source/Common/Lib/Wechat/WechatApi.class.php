<?php
require_once "Utils.class.php";
class WechatApi {
    private $config;
    public function __construct($config) {
        $this->WechatApi($config);
    }

    public function WechatApi($config) {
        $this->config = $config;
    }
    //获取openid的url
    public function getOauthUrl($code) {
        $urlObj["appid"] = $this->config['APPID'];
        $urlObj["secret"] = $this->config['APPSECRET'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = Utils::ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }
    //获取code的 url
    public function getCodeUrl($redirectUrl, $scope='base', $state='STATE')
    {
        $urlObj["appid"] = $this->config['APPID'];
        $urlObj["redirect_uri"] = urlencode($redirectUrl);
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_$scope";
        $urlObj["state"] = "$state#wechat_redirect";
        $bizString = Utils::ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    public function getOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            $baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
            $url = $this->getCodeUrl($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;
        }
    }

    public function getByHttp($url, $timeOut= 30) {
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置ssl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //配置代理
        $proxyHost = $this->config['PROXY_HOST'];
        $proxyPort = $this->config['PROXY_PORT'];
        if (!empty($proxyHost) && $proxyHost != '0.0.0.0' && $proxyPort != 0 ) {
            curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function getOpenidFromMp($code, $timeOut=30)
    {
        $data = $this->getOauthToken($code, $timeOut);
        //取出openid
        $openid = $data['openid'];
        return $openid;
    }

    //获取access_token
    public function getOauthToken($code, $timeOut) {
        $url = $this->getOauthUrl($code);
        $res = $this->getByHttp($url, $timeOut);
        $data = json_decode($res, true);
        if (array_key_exists('errcode', $data) && $data['errcode'] != '') {
            throw new Exception( '错误:'.$data['errmsg'], 1);
        }
        return $data;
    }
    //刷新
    public function refreshToken($token, $timeOut=30) {
        $urlObj['appid'] = $this->config['APPID'];
        $urlObj['grant_type'] = 'refresh_token';
        $urlObj['refresh_token'] = $token;
        $bizString = Utils::ToUrlParams($urlObj);
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?".$bizString;
        $res = $this->getByHttp($url, $timeOut);
        $data = json_decode($res, true);
        if (array_key_exists('errcode', $data) && $data['errcode'] != '') {
            throw new Exception( '错误:'.$data['errmsg'], 1);
        }
        return $data;
    }

    public function getUserinfo($token, $openid, $timeOut=30) {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
        $res = $this->getByHttp($url, $timeOut);
        $data = json_decode($res, true);
        if (array_key_exists('errcode', $data) && $data['errcode'] != '') {
            throw new Exception( '错误:'.$data['errmsg'], 1);
        }
        return $data;
    }
    public function getUserinfoFromMp($code, $timeOut=30) {
        $data = $this->getOauthToken($code, $timeOut);
        $refreshToken = $data['refresh_token'];
        $data = $this->refreshToken($refreshToken);
        $token = $data['access_token'];
        $openid = $data['openid'];
        $data = $this->getUserinfo($token, $openid, $timeOut);
        return $data;
    }

    public function getAccessToken() {
        $appid = $this->config['APPID'];
        $appsecret = $this->config['APPSECRET'];
        if (empty($appid) || empty($appsecret))
            throw new Exception("需要appid和appsecret");
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $return = $this->getByHttp($url, 30);
        $data = json_decode($return, true);
        return $data;
    }

    public function downloadMedia($accessToken, $mediaId) {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$mediaId}";
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置ssl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //配置代理
        $proxyHost = $this->config['PROXY_HOST'];
        $proxyPort = $this->config['PROXY_PORT'];
        if (!empty($proxyHost) && $proxyHost != '0.0.0.0' && $proxyPort != 0 ) {
            curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        }
        //运行curl，结果以jason形式返回
        $body = curl_exec($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);
        $all = array('header'=>$header,
            'body'=>$body);
        return $all;
    }
    public function getTicket($accessToken) {
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$accessToken}&type=jsapi";
        $res = $this->getByHttp($url, 30);
        $data = json_decode($res, true);
        return $data;
    }
    
    public function getSignPackage($params) {
        $jsapiTicket = $params['ticket'];
        // 注意 URL 一定要动态获取，不能 hardcode.
        $url = $params['url'];
    
        $timestamp = time();
        $nonceStr = Utils::getNonceStr();
    
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    
        $signature = sha1($string);
    
        $signPackage = array(
        "appId"     => $this->config['APPID'],
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string
        );
        return $signPackage; 
    }
}
