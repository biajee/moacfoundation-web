<?php
namespace Admin\Controller;
class IndauthController extends BaseController {
    public function index() {
        $model = D('Indauth');
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
        $memberSvc = D('Member', 'Service');
        foreach($list as $k => $v) {
            $member = $memberSvc->getMember($v['id'], 'id,username,nickname,mobile,email');
            $list[$k]['member'] = $member;
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理个人认证');
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
        $model = M('Indauth');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '查看个人认证信息');
        $this->prepare();
        $this->display();
    }
    
    public function update() {
        $id = I('id');
        $model = D('Indauth');
        $old = $model->find($id);
        $data = $model->create();
        $passed = $data['status'] == 1 && $old['status'] !=1;
        if ($passed) {
            $data['authtime'] = time();
        }
        $model->save($data);
        if ($passed) {
            //设置标记
            D('Member')->save(array(
                'id' => $old['id'],
                'isindauth' => 1
            ));
        }
        $this->success('保存成功', U('Indauth/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Indauth')->deleteOne($id);
        $this->success('删除成功', U('Indauth/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Indauth');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Indauth')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Indauth/index'));
    }
    protected function prepare() {
        $statusArr = array('处理中','已通过','未通过');
        $this->assign('statusList', $statusArr);
    }
}