<?php
namespace Admin\Controller;
class TaskController extends BaseController {
    public function index() {
        $model = D('Task');
        $count = $model->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理需求');
        $this->display();
    }
    
    public function detail() {
        $id = I('id');
        $model = D('Task');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '查看详情');
        $this->display();
    }
    
    public function delete() {
        $id = I('id');
        D('Task')->deleteOne($id);
        $this->success('删除成功', U('Task/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Task');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Task')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Task/index'));
    }
}