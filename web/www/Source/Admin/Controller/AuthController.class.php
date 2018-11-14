<?php
namespace Admin\Controller;
class AuthController extends BaseController {
    public function index() {
        $this->display();
    }
    public function login() {
        $checkcode = trim($_POST['seccode']);
        $verify = new \Think\Verify();
        if (!$verify->check($checkcode, 'admin'))
            $this->error('验证码错误');
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        if (empty($username))
            $this->error('请输入用户名');
        if (empty($password))
            $this->error('请输入密码');
        $realpass = pass_encode($username, $password);

        $model = M('Admin');
        $user = $model->field('uid,username,roleids')->where("username='$username' AND password='$realpass'")->find();
        if (empty($user))
            $this->error('用户名或密码错误');
        /*$roleIds = $user['roleids'];
        if (!empty($roleIds)) {
            
            $roles = D('Role')->where(array('id'=>array('in',$roleIds)))->select();
            $rules = array();
            foreach($roles as $role) {
                $tmp = $role['rules'];
                if (empty($tmp))
                    continue;
                if ($tmp=='*') {
                    $rules = '*';
                    break;
                }
                $tmpArr = explode(',',$tmp);
                foreach($tmpArr as $str) {
                    if (!in_array($str, $rules))
                        $rules[] = $str;
                }
            }
        } else {
            $rules = '';    
        }
        $user['rules'] = $rules;
        */
        $user['rules'] = '*';
        session('admin', $user);
        $this->success('登录成功', U('Index/index'));
    }
    public function logout() {
        session('admin',null);
        $this->clientRedirect('Auth/index');
    }
}
