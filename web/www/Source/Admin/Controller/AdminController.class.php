<?php
namespace Admin\Controller;
class AdminController extends BaseController {
    
    public function index() {
        $model = M('Admin');
        $list = $model->order('uid DESC')->select();
        $this->assign('list', $list);
        $this->display();
    }
    
    public function add() {
        $this->prepare();
        $this->display();
    }
    
    public function edit() {
        $id = $_REQUEST['id'];
        $model = M('Admin');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->prepare();
        $this->display();
    }
    
    public function save() {
        $model = M('Admin');
        $_POST['roleids'] = implode(',', $_POST['roleids']);
        $data = $model->create();
        $id = I('id');
        $password = I('password');
        $username = I('username');
        $password = pass_encode($username,$password);
        $cnt = $model->where("username='$username'")->count();
        if ($cnt>0) 
            $this->error('用户名已存在');
        $data = array(
            'username' => $username,
            'password' => $password,
            'regtime' => time()
        );
        $model->add($data);
        $this->success('增加成功',U('Admin/index'));
    }
    
    public function update() {
        $model = M('Admin');
        $id = I('id');
        $_POST['roleids'] = implode(',', $_POST['roleids']);
        $data = $model->create();
        $password = I('password');
        if ($password) {
            $username = $model->where("uid='$id'")->getField('username');
            $password = pass_encode($username,$password);
            $data['password'] = $password;
        } else {
            unset($data['password']);
        }
        $model->where("uid=$id")->save($data);
        $this->success('修改成功',U('Admin/index'));
    }
    
    public function delete() {
        $id = I('id');
        $model = M('Admin');
        $cnt = $model->count();
        if ($cnt<2)
            $this->error('至少要有一个管理员账号');
        $model->where("uid=$id AND islock=0")->delete();
        $this->success('删除成功');
    }
    
    public function check() {
        $username = I('param');
        $model = M('Admin');
        $cnt = $model->where("username='$username'")->count();
        if ($cnt>0)
            $data = array('info'=>'用户名已存在','status'=>'n');
        else
            $data = array('info'=>'可以使用','status'=>'y');
        $this->ajaxReturn($data);        
    }
    
    public function prepare() {
        $roles = $this->cacheSvc->getData('RoleMlt');
        $this->assign('roles',$roles);
    }
}