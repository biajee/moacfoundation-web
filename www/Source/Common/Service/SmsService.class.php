<?php 
namespace Common\Service;
class SmsService {
	public function __construct() {
		$this->SmsService();
	}
	
	public function SmsService() {
		$type = C('SMS_TYPE');
		$config = C('SMS_CONFIG');
		if (empty($type) || empty($config)) {
			E('请先配置短信');
		}
		$class = ucfirst($type);
		import('Common.Lib.Sms.' . $class);
		$this->sender = new $class($config);
	}
	
	public function send($mobile, $message, $param=null) {
		$ret = $this->sender->send($mobile, $message, $param);
		if (!$ret) {
			E($this->sender->getError());
		}
	}
}