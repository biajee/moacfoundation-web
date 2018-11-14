<?php

class Alidayu
{
    protected $error;
    public function __construct($config) {
        $this->Alidayu($config);
    }

    public function Alidayu($config) {
        $this->config = $config;
    }

    public function getError() {
        return $this->error;
    }

    public function send($mobile, $message, $param=null) {
        import('Common.Lib.Alidayu.TopSdk','', '.php');
        $c = new TopClient;
        $c ->appkey = $this->config['appkey'];
        $c ->secretKey = $this->config['secret'];
        $paramstr = '{';
        foreach($param as $k=>$v) {
            $paramstr .= "$k:'$v',";
        }
        $paramstr = rtrim($paramstr, ',');
        $paramstr .= '}';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req ->setExtend( "" );
        $req ->setSmsType( "normal" );
        $req ->setSmsFreeSignName( $this->config['signature']);
        $req ->setSmsParam($paramstr);
        $req ->setRecNum($mobile );
        $req ->setSmsTemplateCode($message );
        $resp = $c ->execute( $req );
        if($resp->result->success)
        {
            return true;
        }
        else
        {
            $this->error = $resp->result->msg;
            return false;
        }

    }
}