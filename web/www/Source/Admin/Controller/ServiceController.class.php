<?php
namespace Admin\Controller;
class ServiceController extends BaseController {
    public function index() {
        $model = D('Service');
        $count = $model->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理服务');
        $this->display();
    }
    
    public function detail() {
        $id = I('id');
        $model = D('Service');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '查看详情');
        $this->display();
    }
    
    public function delete() {
        $id = I('id');
        D('Service')->deleteOne($id);
        $this->success('删除成功', U('Service/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Service');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Service')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Service/index'));
    }
}