<?php
namespace Admin\Controller;
class RoleController extends BaseController {
    public function index() {
        $model = D('Role');
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
        $this->assign('caption','管理角色');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash' => uniqid2(),
            'sortno' => 0
        );
        $this->assign('data', $data);
        $this->assign('caption','增加角色');
        $this->prepare();
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Role');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑角色');
        $this->prepare();
        $this->display();
    }
    
    public function update() {
        $model = D('Role');
        $ruleall = I('ruleall');
        if (empty($ruleall))
            $_POST['rules'] = implode(',', $_POST['rules']);
        else
            $_POST['rules'] = $ruleall;
        $data = $model->create();
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        $this->cache();
        $this->success('保存成功', U('Role/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Role')->deleteOne($id);
        $this->cache();
        $this->success('删除成功', U('Role/index'));
    }
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('Role');
    }
    
    private function prepare() {
        //$rules = load_config(MODULE_PATH.'Conf/rule.php');
        //$this->assign('rules', $rules);
       // unset($rules);
    }
}