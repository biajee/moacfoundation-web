<?php
namespace Admin\Controller;
class EntrustController extends BaseController {
    public function index() {
        $model = D('Entrust');
        $where = array();
        $surname = I('surname');
        $bname = I('bn');
        if (!empty($bname)) {
            $where['title'] = array('like', "%$bname%");
        }
        if (!empty($surname)) {
            $where['contact'] = array('eq', $surname);
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
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理二手房委托');
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
        $this->assign('caption','增加二手房委托');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = M('Entrust');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑二手房委托');
        $this->prepare();
        $this->display();
    }
    
    public function update() {
        $model = D('Entrust');
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
        
        $this->success('保存成功', U('Entrust/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Entrust')->deleteOne($id);
        $this->success('删除成功', U('Entrust/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Entrust');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Entrust')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Entrust/index'));
    }
    protected function prepare() {
        $statusArr = array('未处理','已处理');
        $this->assign('statusList', $statusArr);
    }
}