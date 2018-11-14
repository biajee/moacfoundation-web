<?php
namespace Admin\Controller;
class SupplierController extends BaseController {
    public function index() {
        $model = D('Supplier');
        $count = $model->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理供货商');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'addtime' => time(),
        );
        $this->assign('data', $data);
        $this->assign('caption','增加供货商');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Supplier');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑供货商信息');
        $this->display();
    }
    
    public function update() {
        $model = D('Supplier');
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
        $this->success('保存成功', U('Supplier/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Supplier')->deleteOne($id);
        $this->success('删除成功', U('Supplier/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Supplier');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Supplier')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Supplier/index'));
    }
}