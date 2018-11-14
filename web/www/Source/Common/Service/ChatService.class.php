<?php
namespace Common\Service;
class ChatService extends BaseService{
    
    public function getMessages($where, $limit=null, $order='id DESC' ) {
        $model = M('Message');
        $model->where($where)->order($order);
        if ($limit)
            $model->limit();
        $list = $model->select();
        if ($list) {
            $ids = array();
            foreach($list as $v) {
                if (!$v['status'])
                    $ids[] = $v['id'];
            }
        }

        return $list;
    }
    
    public function getMessageCount($where) {
        return M('Message')->where($where)->count();
    }

    public function buildMessage() {
        return M('Message')->create();
    }

    public function addMessage($data) {
        $data['addtime'] = time();
        $data['id'] = M('Message')->add($data);
        $where = array(
            'memid' => array('eq', $data['fromid']),
            'otherid' => array('eq', $data['toid'])
        );
        $lastmsg = html2text($data['content']);
        //更新联系人
        $contact = $this->getContact($where);
        if (empty($contact)) {
            $item = array(
                'memid' => $data['fromid'],
                'otherid' => $data['toid'],
                'lastchattime' => $data['addtime'],
                'lastmessage' => $lastmsg,
            );
            $this->addContact($item);
        } else {
            $item = array(
                'id' => $contact['id'],
                'lastchattime' => $data['addtime'],
                'lastmessage' => $lastmsg,
            );
            $this->updateContact($item);
        }
        $where = array(
            'memid' => array('eq', $data['toid']),
            'otherid' => array('eq', $data['fromid'])
        );
        //更新联系人2
        $contact = $this->getContact($where);
        if (empty($contact)) {
            $item = array(
                'memid' => $data['toid'],
                'otherid' => $data['fromid'],
                'lastchattime' => $data['addtime'],
                'lastmessage' => $lastmsg,
            );
            $this->addContact($item);
        } else {
            $item = array(
                'id' => $contact['id'],
                'lastchattime' => $data['addtime'],
                'lastmessage' => $lastmsg,
            );
            $this->updateContact($item);
        }
        return $data;
    }
    
    public function getMessage($id) {
        if (is_numeric($id)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = "$mk='$id'";
        return M('Message')->where($where)->find();
    }

    public function markMessage($ids) {
        $where = array(
            'id' => array('in', $ids)
        );
        M('Message')->where($where)->save(array('status'=>1));
    }

    public function getContactByBoth($memid, $otherid) {
        $where = array(
            'memid' => array('eq', $memid),
            'otherid' => array('eq', $otherid)
        );
        return $this->getContact($where);
    }
    public function getContact($where) {
        if (!is_array($where)) {
            return M('Contact')->find($where);
        } else {
            return M('Contact')->where($where)->find();
        }
    }
    public function getContactCount($where) {
        return M('Contact')->where($where)->count();
    }
    public function getContacts($where, $limit=null, $order='lastchattime DESC, id ASC') {
        $model = M('Contact');
        if ($limit)
            $model->limit($limit);
        $list =$model->where($where)->order($order)->select();
        return $list;
    }

    public function addContact($data) {
        $data['addtime'] = time();
        $data['lastviewtime'] = time()-60;
        return M('Contact')->add($data);
    }

    public function updateContact($data, $where=null) {
        return M('Contact')->where($where)->save($data);
    }

    public function updateLastViewTime($id, $time) {
        $where = array(
            'id' => array('eq', $id),
            'lastviewtime' => array('lt', $time)
        );
        $data = array('lastviewtime'=> $time);
        M('Contact')->where($where)->save($data);
    }

    public function getMemStat($uid) {
        $list = M('Contact')->where("memid={$uid}")->field('id,otherid,lastviewtime')->select();
        $msgModel = M('Message');
        $stat = 0;
        foreach($list as $v) {
            $oid = $v['otherid'];
            $time = $v['lastviewtime'];
            if ($oid) {
                $num = $msgModel->where("fromid={$oid} AND toid={$uid} AND addtime > {$time}")->count();
                $stat += $num;
            }

        }
        return $stat;
    }
}