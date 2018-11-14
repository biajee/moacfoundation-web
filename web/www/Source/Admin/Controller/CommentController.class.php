<?php
namespace Admin\Controller;
class CommentController extends BaseController {
    public function index() {
        $model = D('Comment');
        $where = array();
        $title = I('title');
        $username = I('username');
		$uid = I('uid');
		if (!empty($uid)) {
			$where['memid'] = array('eq', $uid);
		}
		$iid = I('iid');
        if ($iid) {
            $where['itemid'] = array('eq', $iid);
        }
        if (!empty($title)) {
            $where['itemtitle'] = array('like', "%$title%");
        }
        if (!empty($username)) {
            $where['memname'] = array('eq', $username);
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
        $this->assign('caption','管理留言');
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
        $this->assign('caption','增加留言');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = M('Comment');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑留言');
        $this->prepare();
        $this->display();
    }
    
    public function update() {
        $model = D('Comment');
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
        
        $this->success('保存成功', U('Comment/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Comment')->deleteOne($id);
        $this->success('删除成功', U('Comment/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Comment');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'check':
                D('Comment')->updateStatus($ids, 1);
                break;
            case 'delete':
                D('Comment')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Comment/index'));
    }
    protected function prepare() {
        $statusArr = array('未审核','已审核');
        $this->assign('statusList', $statusArr);
    }
}