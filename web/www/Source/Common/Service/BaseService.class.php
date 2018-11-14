<?php
namespace Common\Service;
/**
 * Class BaseService 服务基础类
 * @package Common\Service
 */
class BaseService
{
    public function __construct()
    {
        $this->cacheSvc = D('Cache', 'Service');
        if (method_exists($this, '_initialize'))
            $this->_initialize();
    }

    public function bindAttach($key) {
        D('Attachment','Service')->attachTo($key);
    }

    public function delAttach($key) {
        D('Attachment','Service')->delAttachments($key);
    }

    public function getCache($name) {
        return D('Cache', 'Service')->getData($name);
    }

    public function getTimespan($time) {
        $span = time() - $time;
        $day = 3600*24;
        if ($span > $day) {
            $unit = 'day';
            $num = ceil($span/3600/24);
        } elseif ($span > 3600) {
            $unit = 'hour';
            $num = ceil($span/3600);
        } elseif ($span > 60) {
            $unit = 'minute';
            $num = ceil($span/60);
        } else {
            $unit = 'just';
            $num = '';
        }
        $str = L('timespan_',array('unit'=>$unit,'num'=>$num));
        return $str;
    }

    protected function getHash($item) {
        $str = var_export($item, true);
        return md5($str);
    }
}