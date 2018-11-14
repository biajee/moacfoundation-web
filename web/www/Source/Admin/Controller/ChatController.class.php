<?php
namespace Admin\Controller;
class ChatController extends BaseController {
    protected $uid = '10000';
    protected $username = 'hiwibang';
    public function index() {
        $uid = $this->uid;
        $model = service('Chat');
        $where = array(
            'memid' => array('eq', $uid)
        );
        $count = $model->getContactCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $list = $model->getContacts($where, $limit);
        $memModel = service('Member');
        foreach($list as $k=> &$v) {
            $where = array(
                'fromid' => array('eq', $v['otherid']),
                'toid' => $uid,
                'status' => array('eq', 0)
                //'addtime'=>array('gt', $v['lastviewtime'])
            );
            $v['noreadnum'] = $model->getMessageCount($where);
            $v['other'] = $memModel->getMember($v['otherid'],'id,username,nickname,avatar');
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->display();
    }
    public function detail() {
        $cid = I('cid');
        $model = service('Chat');
        $uid = $this->uid;
        $contact = $model->getContact(array(
            'otherid' => array('eq', $cid),
            'memid' => array('eq', $uid)
        ));
        if ($contact) {
            $cid = $contact['otherid'];
        }
        $mModel = service('Member');
        $contact = $mModel->getMember($cid);

        $this->assign('contact', $contact);
        $this->display();
    }
    protected function formatData(& $data) {
        $now = time();
        $time = $data['addtime'];
        $today = date('Ymd', $now);
        $date = date('Ymd', $time);
        $thisYear = date('Y', $now);
        $year = date('Y', $time);
        if ($date==$today) {
            $date = L('today');
        } else {
            if ($year != $thisYear)
                $date = date('Y-n-j', $time);
            else
                $date = date('n-j', $time);
        }
        $data['datestr'] = $date;
        $data['timestr'] = date("h:i:s", $time);
        $data['group'] = date('Ymd', $time);
    }
    public function pull() {
        $cid =  I('cid');
        $uid = $this->uid;
        $model = service('Chat');
        $contact = $model->getContact(array(
            'otherid' => array('eq', $cid),
            'memid' => array('eq', $uid)
        ));
        if (empty($contact)) {
            $this->ajaxError(L('page_not_found'));
        }
        $cid = $contact['otherid'];
        $where = "((fromid=$uid AND toid=$cid) OR (fromid=$cid AND toid=$uid))";
        $where2 = $where . ' AND addtime>'.$contact['lastviewtime'];
        //$where2 = $where . ' AND lastviewtime>'. $contact['lastviewtime'];
        $list = $model->getMessages($where2, null, 'id ASC');
        if ($list) {
            $ids = array();
            $lasttime =0;
            foreach ($list as $k => $v) {
                if ($v['toid'] == $uid) {
                    $ids[] = $v['id'];
                }
                if ($v['addtime'] > $lasttime)
                    $lasttime = $v['addtime'];
                $this->formatData($list[$k]);
            }
            if ($ids) {
                $model->markMessage($ids);
            }
            if ($lasttime)
                $model->updateLastViewTime( $contact['id'],$lasttime);
        }

        $this->ajaxSuccess($list);
    }

    public function latest() {
        $cid =  I('cid');
        $uid = $this->uid;
        $model = service('Chat');
        $contact = $model->getContact(array(
            'otherid' => array('eq', $cid),
            'memid' => array('eq', $uid)
        ));
        if (empty($contact)) {
            $this->ajaxError(L('page_not_found'));
        }
        $cid = $contact['otherid'];
        $where = "((fromid=$uid AND toid=$cid) OR (fromid=$cid AND toid=$uid))";
        //$where2 = $where . ' AND lastviewtime>'. $contact['lastviewtime'];
        $where2 = $where;
        $list = $model->getMessages($where2, '', 'id ASC');
        if ($list) {
            $ids = array();
            $lasttime =0;
            foreach ($list as $k => $v) {
                if ($v['toid'] == $uid) {
                    $ids[] = $v['id'];
                }
                if ($v['addtime'] > $lasttime)
                    $lasttime = $v['addtime'];
                $this->formatData($list[$k]);
            }
            if ($ids) {
                $model->markMessage($ids);
            }
            if ($lasttime)
                $model->updateLastViewTime( $contact['id'],$lasttime);
        }
        $this->ajaxSuccess($list);
    }

    public function save() {
        $model = service('Chat');
        $data = $model->buildMessage();
        $data['fromid'] = $this->uid;
        $data['fromname'] = $this->username;
        $data = $model->addMessage($data);
        $this->formatData($data);
        $this->ajaxSuccess($data);
    }
}