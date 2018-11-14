<?php 
namespace Common\Service;
class WechatService {
    //加载api
    protected function getWechatApi() {
        static $api;
        if (empty($api)) {
            import('Common.Lib.Wechat.WechatApi');
           $config = C('WXPAY_CONFIG');
           $api = new \WechatApi($config);
        }
       return $api;
    }
    //获取access_token
	public function getAccessToken() {
        $api = $this->getWechatApi();
        $tokenArr = S('WxAccessToken');
        if (empty($tokenArr)) {
            $tokenArr = $api->getAccessToken();
            $expires = $tokenArr['expires_in']-200;
            $expireTime = time() + $expires;
            $tokenArr['expire_time'] = $expireTime;
            S('WxAccessToken', $tokenArr, $expires);
        } 
        $accessToken = $tokenArr['access_token'];
        return $accessToken;
    }
    //获取 js ticket
    public function getJsTicket() {
        $api = $this->getWechatApi();
        $ticketArr = S('WxJsTicket');
        if (empty($ticketArr)) {
            $accessToken = $this->getAccessToken();
            $ticketArr = $api->getTicket($accessToken);
            $expires = $ticketArr['expires_in']-200;
            $expireTime = time() + $expires;
            $ticketArr['expire_time'] = $expireTime;
            S('WxJsTicket', $ticketArr, $expires);
        } 
        return $ticketArr['ticket'];
    }
    
    public function getSignPackage() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $api = $this->getWechatApi();
        $ticket = $this->getJsTicket();
        $params['url'] = $url;
        $params['ticket'] = $ticket;
        $package = $api->getSignPackage($params);
        return $package;
    }
	
	public function download($mediaId) {
		$accessToken = $this->getAccessToken();
		$api = $this->getWechatApi();
		return $api->downloadMedia($accessToken, $mediaId);
	}
}