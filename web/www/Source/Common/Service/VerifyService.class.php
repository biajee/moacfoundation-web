<?php
namespace Common\Service;


class VerifyService
{
    protected function getLang($lang, $code, $param=null) {
        static $_lang;
        if (empty($_lang)) {
            $_lang = load_config(CONF_PATH.'verify.php');
        }
        $text = $_lang[$lang][$code];
        if ($param) {
            $replace = array_keys($param);
            foreach($replace as &$v){
                $v = '{$'.$v.'}';
            }
            $text = str_replace($replace, $param, $text);
        }

        return $text;
    }

    public function sendSms($mobile, $type, $param=null) {
        $code = mt_rand(111111, 999999);
        if (substr($mobile, 0, 3)=='+86') {
            $lang = 'cn';
            $mobile2 = substr($mobile, 3);
        } else {
            $lang = 'en';
            $mobile2 = $mobile;
        }
        //$text="【海外邦】您的验证码是#code#，您正在注册成为海外邦用户，感谢您的支持！www.hiwibang.com";
        $text = $this->getLang($lang, 'sms_'.$type, array('code'=>$code));
        $model = service("Sms");
        $model->send($mobile2, $text, $param);
        session($type . 'code/' . $mobile, $code);
    }
    public function sendSms2($mobile, $type, $param=null) {
        $map = array(
            'auth' => 'SMS_13221524',
            'test' => 'SMS_13221523',
            'login' => 'SMS_13221522',
            'loginfail' => 'SMS_13221521',
            'register' => 'SMS_13221520',
            'activity' => 'SMS_13221519',
            'password' => 'SMS_13221518',
            'profile' => 'SMS_13221517'
        );
        $code = mt_rand(111111, 999999);
        $tpl = $map[$type];
        $param['code'] = $code;
        $param['product'] = '海外邦';
        $paramstr = '{';
        foreach($param as $k=>$v) {
            $paramstr .= "$k:'$v',";
        }
        $paramstr = rtrim($paramstr, ',');
        $paramstr .= '}';
        import('Common.Lib.Alidayu.TopSdk','', '.php');
        $c = new \TopClient;
        $c ->appkey = '23476760';
        $c ->secretKey = 'a793a46e2ed09901347a231294bf601b';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req ->setExtend( "" );
        $req ->setSmsType( "normal" );
        $req ->setSmsFreeSignName( "大鱼测试" );
        $req ->setSmsParam($paramstr);
        $req ->setRecNum($mobile );
        $req ->setSmsTemplateCode($tpl );
        $resp = $c ->execute( $req );
        if ($resp->result->success && !$resp->result->code) {
            session($type.'code/'.$mobile, $code);
        }
    }

    public function sendMail($email, $type, $param=null) {
        $code = mt_rand(111111, 999999);
        $subject = $this->getLang('cn', "mail_{$type}_title", null);
        $body = $this->getLang('cn', "mail_{$type}_body", array('code'=> $code));;
        $svc = service('Mail');
        $svc->send($email, $subject, $body);
        session($type.'code/'.$email, $code);
    }
    //发送带二维码图片的邮件
    public function sendcodeMail($email, $type , $img,$id, $param=null) {
        $code = mt_rand(111111, 999999);
        $data = M('Apply')->where('id='.$id)->find();
        $subject = $this->getLang('en', "mail_{$type}_title", null);
        $body = $this->getLang('en', "mail_{$type}_body", $data);
        $svc = service('Mail');
        $svc->codesend($email, $subject, $body,$img);
        session($type.'code/'.$email, $code);
    }

    public function checkSec($type, $item, $code) {
        $checkcode = session($type.'code/'.$item);
        if ($checkcode == $code) {
            return true;
        } else {
            return false;
        }
    }

    public function checkCode($code, $type) {
        $verify = new \Think\Verify();
        return $verify->check($code, $type);
    }

}