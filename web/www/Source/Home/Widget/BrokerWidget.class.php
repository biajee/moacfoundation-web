<?php
namespace Home\Widget;
use Think\Controller;
class BrokerWidget extends Controller
{
    public function service()
    {
        return $this->fetch('Widget/Broker/service');;
    }

    public function building($limit) {
        $where = array(
            'brokerage' => array('neq', '')
        );
        $list = M('Building')->where($where)->order('id desc')->limit($limit)->select();
        $this->assign('list', $list);
        return $this->fetch('Widget/Broker/building');
    }
}