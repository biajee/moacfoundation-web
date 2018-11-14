<?php
namespace Common\Service;
class ClientService {
    
    public function getClients($where) {
        return M('Client')->where($where)->order('id DESC')->select();
    }
    
    public function getClientCount($where) {
        return M('Client')->where($where)->count();
    }
    
    public function getStats($uid=0) {
        $where = array();
        if (!empty($uid))
            $where['memid'] = array('eq', $uid);
        $stats['total'] = $this->getClientCount($where);
        $where['status'] = array('eq',0);
        $stats['process'] = $this->getClientCount($where);
        $where['status'] = array('eq',1);
        $stats['success'] = $this->getClientCount($where);
        $where['status'] = array('eq',2);
        $stats['failed'] = $this->getClientCount($where);
        $where['status'] = array('gt',0);
        $stats['history'] = $this->getClientCount($where);
        return $stats;
    }
    
    public function getClient($id) {
        if (is_numeric($id)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = "$mk='$id'";
        return M('Client')->where($where)->find();
    }
}