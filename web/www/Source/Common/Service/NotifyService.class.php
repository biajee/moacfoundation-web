<?php
namespace Common\Service;
/**
 * Class CreditService 积分服务
 * @package Common\Service
 */
class NotifyService {
    protected function getLang($lang, $code, $param=null) {
        static $_lang;
        if (empty($_lang)) {
            $_lang = load_config(CONF_PATH.'notify.php');
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

    public function notify($uid, $title, $message, $param=null, $type=null) {
        $memberModel = service('Member');
        $user = $memberModel->getMember($uid);
        $group = array(
            'tradenew' => array('trade_new'),
            'tradechange' => array('trade_accept','trade_refuse','trade_pay','trade_start','trade_finish','trade_inspect','trade_complate','trade_not_review'),
            'follow' => array('follow_new'),
            'comment' => array('comment'),
        );
        $permit = '';
        foreach($group as $k=>$v) {
            if (in_array($message, $v)) {
                $permit = $k;
                break;
            }
        }
        if (!empty($permit) && empty($user['pn'.$permit])) {
            return;
        }
        $param['name'] = $user['username'];
        if (empty($type))
            $type = 'chat,mail,sms';
        $type = explode(',', $type);

        if (in_array('chat', $type)) {
            $this->sendMessage($user, $message, $param);
        }
        if (in_array('sms', $type)) {
            $cc = $user['countrycode'];
            $mobile = $user['mobile'];
            if ($mobile) {
                $this->sendSMS($cc.$mobile, $message, $param);
            }

        }
        if (in_array('mail', $type)) {
            $email = $user['email'];
            if ($email) {
                $this->sendMail($email,$title, $message, $param);
            }
        }

    }

    public function sendSMS($mobile, $message, $param) {
        if (substr($mobile, 0, 3)=='+86') {
            $lang = 'cn';
            $mobile2 = substr($mobile, 3);
        } else {
            $lang = 'en';
            $mobile2 = $mobile;
        }
        $message = $this->getLang($lang, 'sms_'.$message, $param);
        $svc = service('Sms');
        $svc->send($mobile2, $message, $param);
    }

    public function sendMail($email, $title, $message, $param) {
        $lang = 'cn';
        $subject = $this->getLang($lang,'mail_'.$title);
        $body = $this->getLang($lang,'mail_'.$message, $param);
        $svc = service('Mail');
        $svc->send($email, $subject, $body);
    }

    public function sendMessage($user, $message, $param) {
        $model = service('Chat');
        $message = $this->getLang('cn','chat_'.$message, $param);
        $data = array(
            'fromid' => 10000,
            'fromname' => 'hiwaibang',
            'toid' => $user['id'],
            'toname' => $user['username'],
            'content' => $message,
        );
        $model->addMessage($data);
    }

}