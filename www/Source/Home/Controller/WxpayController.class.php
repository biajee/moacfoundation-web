<?php
namespace Home\Controller;
class WxpayController {
	/*
	应该传递过来 $_GET['code']
	https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect 获取
	*/
	public function index() {
		$_GET['code'] = "123444";
		import('@.Lib.WxpayJsApi');
		$tools = new \WxpayJsApi();
		$openId = $tools->GetOpenid();
		
		$input = new \WxPayUnifiedOrder();
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url(U('Wxpay/notify'));
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = \WxPayApi::unifiedOrder($input);
		$jsApiParameters = $tools->GetJsApiParameters($order);
		$this->assign('jsApiParameters',$jsApiParameters);
		$this->display();
	}
	
	public function notify() {
		
		import('@.Lib.WxpayNotifyCallBack');
		$notify = new \WxpayNotifyCallBack();
		$notify->Handle(false);
	}
}

