<?php
namespace Common\Service;
/**
 * Class TradeService
 * @package Common\Service
 */
class TradeService extends BaseService
{

    public function buildTrade()
    {
        return M('Trade')->create();
    }

    /**
     * 添加交易
     * @param $data array 数据
     * @return int 交易id
     */
    public function addTrade($data)
    {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        $data['lasttime'] = $data['addtime'];
        $data['updatetime'] = $data['addtime'];
        $id = M('Trade')->add($data);
        $tradeno = time().str_padzero($id, 2);
        M('Trade')->save(array(
            'id' => $id,
            'tradeno' => $tradeno
        ));
        //更新附件信息
        $key = 'Trade/' . $id;
        $this->bindAttach($key);
        //更新统计
        if ($data['tradetype'] == 'buy') {
            $starter = $data['buyerid'];
        } else {
            $starter = $data['sellerid'];
        }
        service('Member')->updateStat($starter, 'tradenum');
        return $id;
    }

    /**
     * @param $data 保存的数据
     * @return int 影响条数
     */
    public function updateTrade($data)
    {
        if (empty($data['id'])) {
            E('信息不存在');
        }
        //$data['updatatime'] = time();
        $result = M('Trade')->save($data);
        $key = 'Trade/' . $data['id'];
        $this->bindAttach($key);
        return $result;
    }

    /**
     * @param $where
     * @param string $limit
     * @param string $order
     * @param string $fields
     * @return mixed
     */
    public function getTrades($where, $limit = '10', $order = 'id DESC', $fields = '')
    {
        if (empty($fields))
            $fields = '*';
        $data['updatetime'] = time();
        $list = M('Trade')->field($fields)
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();
        if (!empty($list)) {
            for ($k = 0; $k < count($list); $k++) {
                $this->formatData($list[$k]);
            }
        }

        return $list;
    }

    /**
     * @param $where
     * @return mixed
     */
    public function getTradeCount($where)
    {
        return M('Trade')->where($where)->count();
    }
    public function getCartCount($where) {
        return M('Cart')->where($where)->count();
    }

    /**
     * @param $where mixed
     * @param string $fields
     * @return mixed
     */
    public function getTrade($where, $fields = '*')
    {
        if (is_array($where))
            $data = M('Trade')->field($fields)->where($where)->find();
        else
            $data = M('Trade')->field($fields)->find($where);
        if (!empty($data)) {
            $this->formatData($data);
        }

        return $data;
    }

    /**
     * @param $id
     * @param string $item
     * @param int $count
     * @return mixed
     */
    public function updateStat($id, $item = 'viewnum', $count = 1)
    {
        $model = M('Trade');
        $data = array('id' => $id,
            $item => array('exp', "{$item}+{$count}")
        );
        $model->save($data);
        $num = $model->where(array('id' => array('eq', $id)))->getField($item);
        return $num;
    }

    /**
     * @param $data
     */
    protected function formatData(&$data)
    {
        static $district = null;
        static $realm = null;
        static $status = null;
        $cache = service('Cache');
        if ($data['images']) {
            $data['imagelist'] = explode(',', $data['images']);
        }
        if (empty($district)) {
            $district = $cache->getData('DistrictMlt');
        }
        if (empty($realm)) {
            $realm = $cache->getData('RealmMlt');
        }
        if (empty($status)) {
            $status = $cache->getData('TradeStatusMlt');
        }
        $data['statusstr'] = $status[$data['status']]['title'];
        $data['countrystr'] = $district[$data['country']]['title'];
        $data['citystr'] = $district[$data['country']]['title'];
        $data['realmstr'] = $realm[$data['realm']]['title'];
        $data['realm2str'] = $realm[$data['realm2']]['title'];
        if ($data['content'] != '') {
            $data['content'] = nl2br($data['content']);
        }
        $this->runCron($data);
    }
    //定时任务
    protected function runCron(&$data) {
        $now = time();
        ////检测交易请求是否超时
        if ($data['status'] == 'unconfirmed' && $data['expire']>0 && $data['expire'] < $now) {
            $update = array(
                'id' => $data['id'],
                'status' => 'closed',
                'lasttime' => $now
            );
            $this->updateTrade($update);
            $log = array(
                'tradeid' => $data['id'],
                'memid' => '0',
                'memname' => 'system',
                'oldstatus' => $data['status'],
                'action' => 'close',
                'memo' => 'Closed by system'
            );
            $this->addLog($log);
            $data['status'] = 'closed';
            $data['lasttime'] = $now;
            $data['updatetime'] = $now;
        }
        //检测是否超过60天未评价
        $limittime = 60*86400;
        $timespan = $now - $data['lasttime'];
        $update2 = array();
        if ($data['status'] == 'complated' && $timespan > $limittime) {
            //需求方未评价
            if (empty($data['isbuyerreview']) && empty($data['isbuyerreviewnotify'])) {
                $this->notify($data['buyerid'], '', 'trade_not_review', array('title'=>$data['title']), 'chat');
                $update2['isbuyerreviewnotify'] = 1;
            }
            //服务方未评价
            if (empty($data['issellerreview']) && empty($data['issellerreviewnotify'])) {
                $this->notify($data['sellerid'], '', 'trade_not_review', array('title'=>$data['title']), 'chat');
                $update2['issellerreviewnotify'] = 1;
            }
            //更新未评价通知状态
            if (!empty($update2)) {
                $update2['id'] = $data['id'];
                $this->updateTrade($update2);
            }
        }

    }
    /**
     * @param null $uid
     * @return mixed
     */
    public function getStats($uid = null)
    {
        $where = array(
            'module' => array('eq', 'service')
        );
        if ($uid) {
            $where['memid'] = array('eq', $uid);
        }
        $stat['service'] = M('Trade')->where($where)->count();
        $where['module'][1] = 'task';
        $stat['task'] = M('Trade')->where($where)->count();
        $where['module'][1] = 'news';
        $stat['news'] = M('Trade')->where($where)->count();
        return $stat;
    }

    public function getLogs($tid) {
        $list = M('Tradelog')->where("tradeid=$tid")->order('id ASC')->select();
        foreach($list as &$v) {
            $v['actionstr'] = L('trade_action_'.$v['action']);
        }
        return $list;
    }

    public function addLog($log)
    {
        $log['addtime'] = time();
        M('Tradelog')->add($log);
    }

    public function accept($order) {
        if ($order['status'] != 'unconfirmed') {
            E(L('trade_action_failed'));
        }
        $action = 'accept';
        $newStatus = 'accepted';
        $update = array(
            'id' => $order['id'],
            'status' => $newStatus,
            'lasttime' => time()
        );
        $this->updateTrade($update);
        if ($order['tradetype'])
            $othertype = 'sellerer';
        else
            $othertype = 'buyer';
        $log = array(
            'tradeid' => $order['id'],
            'memid' => $order[$othertype.'id'],
            'memname' => $this->member[$othertype.'name'],
            'oldstatus' => $order['status'],
            'action' => $action
        );
        $this->addLog($log);

    }
    public function refuse($order) {
        if ($order['status'] != 'unconfirmed') {
            E(L('trade_action_failed'));
        }
        $action = 'refuse';
        $newStatus = 'refused';
        $update = array(
            'id' => $order['id'],
            'status' => $newStatus,
            'lasttime' => time()
        );
        $this->updateTrade($update);
        if ($order['tradetype'])
            $othertype = 'sellerer';
        else
            $othertype = 'buyer';
        $log = array(
            'tradeid' => $order['id'],
            'memid' => $order[$othertype.'id'],
            'memname' => $this->member[$othertype.'name'],
            'oldstatus' => $order['status'],
            'action' => $action
        );
        $this->addLog($log);
    }

    public function getPrestige($uid) {
        $where = array(
            'otherid' => array('eq', $uid)
        );
        $num = M('Review')->where($where)->avg('mark');
        return $num;
    }

    public function getReview($where) {
        return M('Review')->where($where)->find();
    }

    public function getReviews($where, $limit=null) {
        $model = M('Review');
        $model->where($where);
        if ($limit)
            $model->limit($limit);
        return $model->order('id DESC')->select();
    }
    public function getMemReviews($uid, $limit=null) {
        $model = M('Review');
        $model->where("otherid=$uid");
        if ($limit)
            $model->limit($limit);
        return $model->order('id DESC')->select();
    }

    public function getMemRole($uid, $trade) {
        if ($uid == $trade['buyerid']) {
            $mytype = 'buyer';
        } else {
            $mytype = 'seller';
        }
        return $mytype;
    }
    public function checkReview($mytype, $trade) {
        if ($trade['status'] != 'complated') {
            E(L('trade_can_not_review'));
        }
        $field = 'is' . $mytype . 'review';
        if (($trade[$field])) {
            E(L('trade_review_already'));
        }
    }
    public function addReview($param)
    {
        $uid = $param['uid'];
        $id = $param['tradeid'];
        $model = service('Trade');
        $trade = $model->getTrade($id);
        $mytype = $this->getMemRole($uid, $trade);
        $this->checkReview($mytype, $trade);
        $content = $param['content'];
        if ($mytype == 'buyer')
            $othertype = 'seller';
        else
            $othertype = 'buyer';
        $otherid = $trade[$othertype . 'id'];
        $data = array(
            'tradeid' => $id,
            'tradetitle' => $trade['title'],
            'mytype' => $mytype,
            'memid' => $trade[$mytype . 'id'],
            'memname' => $trade[$mytype . 'name'],
            'otherid' => $trade[$othertype . 'id'],
            'othername' => $trade[$othertype . 'name'],
            'mark' => $param['mark'],
            'content' => $content,
            'addtime' => time()
        );
        M('Review')->add($data);
        $update = array(
            'id' => $id,
            'is'.$mytype.'review' => 1
        );
        $this->updateTrade($update);
        //更新信誉值
        $prestige = $this->getPrestige($otherid);
        M('Member')->save(array(
            'id' => $otherid,
            'prestige' => $prestige
        ));
    }

    public function checkComplain($uid, $trade) {
        $where = array(
            'memid' => array('eq', $uid),
            'tradeid' => array('eq', $trade['id'])
        );
        $cnt = M('Complain')->where($where)->count();
        if ($cnt) {
            E(L('trade_complain_already'));
        }
    }
    //保存投诉
    public function addComplain($param) {
        $uid = $param['uid'];
        $id = $param['tradeid'];
        $model = service('Trade');
        $trade = $model->getTrade($id);
        $mytype = $this->getMemRole($uid, $trade);
        $this->checkComplain($uid, $trade);
        $content = $param['content'];
        if ($mytype == 'buyer')
            $othertype = 'seller';
        else
            $othertype = 'buyer';
        $data = array(
            'tradeid' => $id,
            'tradetitle' => $trade['title'],
            'mytype' => $mytype,
            'memid' => $trade[$mytype . 'id'],
            'memname' => $trade[$mytype . 'name'],
            'otherid' => $trade[$othertype . 'id'],
            'othername' => $trade[$othertype . 'name'],
            'content' => $content,
            'addtime' => time()
        );
        M('Complain')->add($data);
    }
    public function getComplains($where, $limit=null) {
        $model = M('Complain');
        $model->where($where);
        if ($limit)
            $model->limit($limit);
        return $model->order('id DESC')->select();
    }

    protected function notify($uid,$title, $message, $param, $type) {
        try {
            $model = service('Notify');
            $model->notify($uid,$title, $message, $param, $type);
        } catch (\Exception $e) {

        }
    }
}