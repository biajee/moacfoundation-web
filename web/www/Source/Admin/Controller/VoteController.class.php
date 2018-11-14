<?php
namespace Admin\Controller;

class VoteController extends BaseController {
	public function index() {
        $model = D('Vote');
        $where = '1=1';
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','投票管理');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash' => uniqid2(),
            'style' => 0,
            'votenum' => 0,
            'addtime' => time(),
            'sortno' => 0,
            'status' => 1
        );
        $this->assign('data', $data);
        $this->assign('caption','增加投票信息');
        $this->display('edit');
    }

    public function detail() {
        $id = I('id');
        $model = D('Vote');
        $data = $model->find($id);
        $this->assign('data', $data);
        $itemlist = M('Voteitem')->where("voteid=$id")->order('votenum DESC')->select();
        $this->assign('itemList', $itemlist);
        $this->assign('caption', '投票信息');
        $this->display();
    }

    public function edit() {
        $id = I('id');
        $model = D('Vote');
        $data = $model->find($id);
        $this->assign('data', $data);
        $itemlist = M('Voteitem')->where("voteid=$id")->select();
        $this->assign('itemList', $itemlist);
        $this->assign('caption', '编辑投票信息');
        $this->display();
    }
    
    public function update() {
        $model = D('Vote');
        $data = $model->create();
		if (empty($data['status']))
			$data['status'] = 0;
        if (empty($data['addtime']))
            $data['addtime'] = time();
        else
            $data['addtime'] = strtotime($data['addtime']);
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        //处理选项信息
        $itemModel = M('Voteitem');
        $items = $_POST['item'];
        if ($items) {
            foreach($items as $k=>$v) {
                if ($v['del'])
                    $itemModel->delete($v['id']);
                else
                    $itemModel->save($v);
            }
        }
        $newitems = $_POST['newitem'];
        if ($newitems) {
            foreach($newitems as $v) {
                if ($v['title']) {
                    $v['voteid'] = $data['id'];
                    $data['votenum'] = intval($data['votenum']);
                    $data['sortno'] = intval($data['sortno']);
                    $itemModel->add($v);
                }
            }
        }
        $this->success('保存成功', U('Vote/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Vote')->deleteOne($id);
        $this->success('删除成功', U('Vote/index'));
    }
}