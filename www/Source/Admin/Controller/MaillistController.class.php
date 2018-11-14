<?php
namespace Admin\Controller;
class MaillistController extends BaseController {
    public function index() {
        $model = D('Maillist');
        $count = $model->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理链接');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash'=> uniqid2(),
            'addtime' => time(),
        );
        $this->assign('data', $data);
        $this->assign('caption','增加链接');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Maillist');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑链接');
        $this->display();
    }
    
    public function update() {
        $model = D('Maillist');
        $data = $model->create();
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        if (empty($data['id'])) {
            $data['status'] = 1;
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        
        $this->success('保存成功', U('Maillist/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Maillist')->deleteOne($id);
        $this->success('删除成功', U('Maillist/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Maillist');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Maillist')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Maillist/index'));
    }
}