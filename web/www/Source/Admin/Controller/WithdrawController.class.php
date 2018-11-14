<?php
namespace Admin\Controller;
class WithdrawController extends BaseController {
    public function index() {
        $model = D('Withdraw');
        $where = array();
        $surname = I('surname');
        if (!empty($surname)) {
            $where['surname'] = array('eq', $surname);
        }
        $status = I('status');
        if ($status != '')
            $where['status'] = array('eq', $status);
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $brokerSvc = D('Broker', 'Service');
        foreach($list as $k => $v) {
            $broker = $brokerSvc->getBroker($v['memid']);
            $list[$k]['broker'] = $broker;
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理推荐楼盘');
        $this->prepare();
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash'=> uniqid2(),
            'addtime' => time(),
        );
        $this->assign('data', $data);
        $this->prepare();
        $this->assign('caption','增加看房申请');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = M('Withdraw');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑看房申请信息');
        $this->prepare();
        $this->display();
    }
    
    public function update() {
        $model = D('Withdraw');
        $data = $model->create();
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        
        $this->success('保存成功', U('Withdraw/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Withdraw')->deleteOne($id);
        $this->success('删除成功', U('Withdraw/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Withdraw');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Withdraw')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Withdraw/index'));
    }
    protected function prepare() {
        $statusArr = array('处理中','已处理','已失败');
        $this->assign('statusList', $statusArr);
    }
}