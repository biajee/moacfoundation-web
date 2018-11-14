<?php
namespace Common\Service;
class LocationService {
    public function fromIP($ip = '') {
        if (empty($ip)) {
            $ip = get_client_ip();
        }
        $url = 'http://freeapi.ipip.net/'.$ip;
        $text = $this->httpGet($url);

        return json_decode($text);
    }

    public function httpGet($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//严格认证
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
}