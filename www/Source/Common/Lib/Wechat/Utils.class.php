<?php
class Utils {
    public static function toXML($data) {
        $xml = "<xml>";
        foreach($data as $k=>$v) {
            if ( is_numeric($v))
                $xml .= "<$k>$v</$k>";
            else
                $xml .= "<$k><![CDATA[$v]]></$k>";
        }
        $xml .= '</xml>';
        return $xml;
    }

    public static function fromXML($xml) {
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    public static function toUrlParams($data) {
        $str = '';
        foreach($data as $k=>$v) {
            if ($k!='sign' && $v!='' && !is_array($v)) {
                $str .= "$k=$v&";
            }
        }
        $str = trim($str, '&');
        return $str;
    }

    //获取毫秒级别时间戳
    public static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    
    public static function log($data, $sign='log') {
        /*if (is_array($data))
            $data = self::toXML($data);
        $filename = 'Logs/Wxpay/'.$sign.'_'.date('YmdHis').'.txt';
        file_put_contents($filename, $data);
        */
    }
}
