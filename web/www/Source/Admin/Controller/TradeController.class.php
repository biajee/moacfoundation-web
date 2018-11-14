<?php
namespace Admin\Controller;
class TradeController extends BaseController {
    public function index() {
        $model = D('Trade');
        $where = array();
        $keyword = I('keyword');
        $status = I('status');
        $uid = I('uid');
        if ($uid) {
            $map['buyerid'] = array('eq', $uid);
            $map['sellerid'] = array('eq', $uid);
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        $tradeno = I('tradeno');
        if ($tradeno) {
            $where['tradeno'] = array('eq', $tradeno);
        }
        $username = I('username');
        if ($username) {
            if (valid_mobile($username))
                $where2['mobile'] = array('eq', $username);
            elseif (valid_email($username))
                $where2['email'] = array('eq', $username);
            else {
                $where2['username'] = array('eq', $username);
                $where2['nickname'] = array('eq', $username);
                $where2['_logic'] = 'or';
            }
            $uids = M('Member')->where($where2)->getField('id', true);
            if ($uids) {
                $map['buyerid'] = array('in', $uids);
                $map['sellerid'] = array('in', $uids);
                $map['_logic'] = 'or';
                $where['_complex'] = $map;
            } else {
                $where['buyerid'] = 0;
            }
        }
        if (!empty($status)) {
            $where['status'] = array('eq', $status);
        }
        if (!empty($keyword))
            $where['title'].= array('like',"%{$keyword}%");
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
            ->where($where)
            ->order("id DESC")
            ->limit($pager->firstRow.','.$pager->listRows)
            ->select();
        $statusMlt = $this->cacheSvc->getData('TradeStatusMlt');
        foreach($list as &$v) {
            $v['statusstr'] = $statusMlt[$v['status']]['title'];
        }
        $this->assign('statusList', $statusMlt);
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->prepare();
        $this->assign('orderList', array(
            array('text'=>'交易时间↓', 'value'=>'id DESC'),
            array('text'=>'交易时间↑', 'value'=>'id ASC'),
            array('text'=>'更新时间↓', 'value'=>'updatetime DESC'),
            array('text'=>'更新时间↑', 'value'=>'updatetime ASC'),
            array('text'=>'交易金额↓', 'value'=>'amount DESC'),
            array('text'=>'交易金额↑', 'value'=>'amount ASC')
        ));
        $this->assign('caption','管理交易');
        $this->display();
    }
    
    public function detail() {
        $id = I('id');
        $model = service('Trade');
        $data = $model->getTrade($id);
        $data['logs'] = $model->getLogs($id);
        $data['reviews'] = $model->getReviews(array('tradeid'=>array('eq',$id)));
        $data['complains'] = $model->getComplains(array('tradeid'=>array('eq',$id)));
        $this->assign('data', $data);
        $this->assign('caption', '查看详情');

        $this->display();
    }
    
    public function delete() {
        $id = I('id');
        D('Trade')->deleteOne($id);
        $this->success('删除成功', U('Trade/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Trade');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Trade')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Trade/index'));
    }
    //在线支付未成功使用这个功能
    public function pay() {
        $id = I('id');
        $model = service('Trade');
        $data = $model->getTrade($id);
        if ($data['status'] != 'accepted') {
            $this->showError('无效操作');
        }
        $this->assign('today', time());
        $this->assign('data', $data);
        $payList = $this->cacheSvc->getData('PayMethodMlt');
        $this->assign('payList', $payList);
        $this->display();
    }

    public function paid() {
        $id = I('id');
        $model = service('Trade');
        $old = $model->getTrade($id);
        if ($old['status'] != 'accepted') {
            $this->ajaxError('无效操作');
        }
        $paytime = I('paytime');
        if ($paytime)
            $paytime = strtotime($paytime);
        else
            $paytime = time();
        $data = array(
            'id' => $id,
            'paymethod' => I('paymethod'),
            'realpay' => I('realpay'),
            'paytime' => $paytime,
            'status' => 'paid',
            'lasttime' => time()
        );
        $model->updateTrade($data);
        $log = array(
            'tradeid' => $id,
            'memid' => $old['sellerid'],
            'memname' => $old['sellername'],
            'oldstatus' => $old['status'],
            'action' => 'pay'
        );
        $model->addLog($log);
        try {
            $this->notify($old['sellerid'], 'trade_title', 'trade_pay', array('title'=>$old['title'], 'tradeno'=>$old['tradeno'], 'url'=>U('Trade/detail/'.$old['id'], null, true)));
        } catch(\Exception $e) {

        }
        $this->ajaxSuccess();
    }
    //给服务方打款完成交易
    public function complate() {
        $id = I('id');
        $model = service('Trade');
        $data = $model->getTrade($id);
        if ($data['status'] != 'inspected') {
            $this->showError('无效操作');
        }
        $this->assign('today', time());
        $this->assign('data', $data);
        $this->display();
    }

    public function complated() {
        $id = I('id');
        $model = service('Trade');
        $old = $model->getTrade($id);
        if ($old['status']!='inspected') {
            $this->ajaxError('无效操作');
        }
        $realwithdraw = I('realwithdraw');
        $realwithdraw = floatval($realwithdraw);
        if (empty($realwithdraw)) {
            $this->ajaxError('请输入有效金额');
        }
        $paytime = I('withdrawtime');
        if ($paytime)
            $paytime = strtotime($paytime);
        else
            $paytime = time();
        $data = array(
            'id' => $id,
            'status' => 'complated',
            'realwithdraw' => $realwithdraw,
            'withdrawtime' => $paytime,
            'lasttime' => time()
        );
        $model->updateTrade($data);
        //记录日志
        $log = array(
            'tradeid' => $id,
            'memid' => 0,
            'memname' => 'system',
            'oldstatus' => $old['status'],
            'action' => 'complate',
            'memo' => '['.$this->admin['username'].']' . I('memo')
        );
        $model->addLog($log);
        //记录资金流水
        $memModel = service('Member');
        $daybook = array(
            'memid' => $old['buyerid'],
            'memname' => $old['buyername'],
            'money' => -1 * $realwithdraw,
            'memo' => $old['title'],
            'addtime' => time()
        );
        $memModel->addDaybook($daybook);
        $daybook = array(
            'memid' => $old['sellerid'],
            'memname' => $old['sellername'],
            'money' => $realwithdraw,
            'memo' => $old['title'],
            'addtime' => time()
        );
        $memModel->addDaybook($daybook);
        $this->notify($old['sellerid'], 'trade_title', 'trade_complate', array('title'=>$old['title'], 'tradeno'=>$old['tradeno'], 'url'=>U('Trade/detail/'.$old['id'], null, true)));
        $this->ajaxSuccess();
    }
    public function prepare() {
        $this->assign('districtList', $this->cacheSvc->getData('DistrictMlt'));
        $this->assign('realmList', $this->cacheSvc->getData('RealmMlt'));
    }
}