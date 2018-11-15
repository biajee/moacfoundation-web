<?php
return array(
    'LOG_RECORD' => false,
    //系统配置
	'MODULE_ALLOW_LIST'    =>    array('Admin','Wap','Home'),
	'DEFAULT_MODULE'       =>    'Home',
	'MODULE_DENY_LIST'   => array('Common'),
    'URL_MODEL'=>2,
	'APP_SUB_DOMAIN_DEPLOY' =>  1,
	'APP_SUB_DOMAIN_RULES'  => array(
		'm.gofun-tj.com'   => 'Wap',
	),
    'DEFAULT_FILTER' => '',
    'DB_TYPE'=>'mysql',
    'DB_HOST'=>'127.0.0.1', 
    'DB_NAME'=>'moac',
    'DB_USER'=>'moac',
    'DB_PWD'=>'moac@123',
    'DB_PORT'=>'',
    'DB_PREFIX'=>'edb_',
	//缓存配置
	'DATA_CACHE_TYPE' => 'File',
	'MEMCACHED_SERVER' => array(
		array('127.0.0.1', 11211, 0)
	),
	//安全相关
	'APP_KEY'=>'',
	//微信支付配置
    'WXPAY_CONFIG'=>array(
        'APPID' => '',
        'APPSECRET'=>'',
        'MCHID'=>'',
        'KEY'=>'',
        'SSLCERT_PATH'=>COMMON_PATH.'Lib/Wechat/cert/apiclient_cert.pem',
        'SSLKEY_PATH'=>COMMON_PATH.'Lib/Wechat/cert/apiclient_key.pem',
        'PROXY_HOST'=>'0.0.0.0',
        'PROXY_PORT'=>0,
        'REPORT_LEVENL' => 1
    ),
    'ALIPAY_CONGIF' =>  array(
        'SERVICE' => 'alipay.wap.create.direct.pay.by.user',
        'PARTNER' => '', //商家id
        'SELLER_ID' => '', //支付宝账号
        'KEY' => '',
        'PAYMENT_TYPE' => '1',
        'INPUT_CHARSET' => 'utf-8',
        'SIGN_TYPE' => 'MD5',
        'CACERT' => COMMON_PATH.'Lib/Alipay/cert/cert.pem'
    ),
    //短信设置
    /*'SMS_TYPE'=>'alidayu',
    'SMS_CONFIG'=> array(
        'appkey' => '23476760',
        'secret' => 'a793a46e2ed09901347a231294bf601b',
        'signature' => '大鱼测试',
    ),*/
    'SMS_TYPE'=>'yunpian',
    'SMS_CONFIG'=> array(
        'appkey' => 'fac9357f72b00e32cf1b69e1803e3c8f',
        'secret' => ''
    ),
    //邮件配置
    'MAIL_CONFIG' => array(
        'server' => 'smtp.mxhichina.com',
        'port' => 25,
        'secure' => '',
        'user' => '',
        'pwd' => '',
        'from' => '',
        'name' => ''
    ),
	//上传设置
	'UPLOAD_PATH' => './upload/',
	'UPLOAD_URL' => '/upload',
    //系统信息
    'SOFT_NAME'=>'网站信息管理系统 - Powerd By EasyIMS',
	'SOFT_VERSION'=>'2.7',
    'SOFT_SUPPORT'=>'<a href="http://www.hiwibang.com" target="_blank">hiwibang</a>&nbsp;',
    'SOFT_OEM'=>'',
);
