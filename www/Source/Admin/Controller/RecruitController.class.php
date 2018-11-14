<?php
namespace Admin\Controller;
class RecruitController extends BaseController {
    public function index() {
        $model = D('Recruit');
        $where = '1=1';
        $keyword = I('keyword');
        $catid = I('catid');
        if (!empty($catid)) {
            $catid = tree_get_leaves($typelist, $catid);
            $where .= " AND catid IN($catid)";
        }
        if (!empty($keyword))
            $where .= " AND title LIKE '%{$keyword}%'"; 
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
        $this->assign('caption','招聘信息');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash'=> uniqid2(),
            'addtime' => time(),
        );
        $this->assign('data', $data);
        $this->assign('caption','增加招聘信息');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Recruit');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑招聘信息');
        $this->display();
    }
    
    public function update() {
        $model = D('Recruit');
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
        $this->success('保存成功', U('Recruit/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Recruit')->deleteOne($id);
        $this->success('删除成功', U('Recruit/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
        $model = D('Recruit');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Recruit')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Recruit/index'));
    }
    
}