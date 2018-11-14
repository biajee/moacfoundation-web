<?php
namespace Admin\Controller;
class AdminlogController extends BaseController {
    public function index() {
        $model = D('Adminlog');
        $where = array();
        $title = I('title');
        $username = I('username');
		$module = I('module');
        $optype = I('optype');
		if (!empty($module)) {
			$where['module'] = array('eq', $module);
		}
        if (!empty($title)) {
            $where['itemtitle'] = array('like', "%$title%");
        }
        if (!empty($username)) {
            $where['admname'] = array('eq', $username);
        }
        if (!empty($optype)) {
            $where['optype'] = array('eq', $optype);
        }
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $module = $this->cacheSvc->getData('LogModuleMlt');
        $optype = $this->cacheSvc->getData('LogOptypeMlt');
        foreach($list as $k=>$v) {
            $list[$k]['optypename'] = $optype[$v['optype']]['title'];
            $list[$k]['modulename'] = $module[$v['module']]['title'];
        }
        $this->assign('optypeList', $optype);
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理评论');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash'=> uniqid2(),
            'addtime' => time(),
        );
        $this->assign('data', $data);
        $this->assign('caption','增加评论');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = M('Adminlog');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑评论');
        $this->display();
    }
    
    public function update() {
        $model = D('Adminlog');
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
        
        $this->success('保存成功', U('Adminlog/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Adminlog')->deleteOne($id);
        $this->success('删除成功', U('Adminlog/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Adminlog');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'check':
                D('Adminlog')->updateStatus($ids, 1);
                break;
            case 'delete':
                D('Adminlog')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Adminlog/index'));
    }
}