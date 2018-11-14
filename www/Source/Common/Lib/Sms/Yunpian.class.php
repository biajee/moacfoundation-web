<?php

class Yunpian
{
    protected $error;
    public function __construct($config) {
        $this->Yunpian($config);
    }

    public function Yunpian($config) {
        $this->config = $config;
    }

    public function getError() {
        return $this->error;
    }
    public function send($mobile, $message, $param=null) {
        import('Common.Lib.Yunpian.YunpianAutoload','', '.php');
        // 发送单条短信
        $smsOperator = new \SmsOperator();
        //$data['mobile'] = urlencode($mobile);
        $data['mobile'] = $mobile;
        $data['text'] = $message;
        $response = $smsOperator->single_send($data);
        $res = $response->responseData;
        if ($response->isSuccess()) {

            if ($res['code'] == 0) {
                return true;
            } else {
                $this->error = $res['msg'];
                return false;
            }
        } else {
            $this->error = $res['msg'];
            return false;
        }

    }
}