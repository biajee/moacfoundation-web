<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
    public $poweredby;
    protected $cacheSvc = null;
    protected $arctypeSvc = null;
    protected $perview = array();
    protected $admin = null;
    protected function clientRedirect($url) {
        $url = U($url);
        $text = "<script>window.top.location='$url';</script>";
        die($text);
    } 
    protected function showSuccess($msg) {
        $script = '<script>parent.$.freebox.close(); parent.location.reload();</script>';
        $this->show($script);
    }
    protected function showError($msg) {
        $script = '<script>alert("' . $msg . '"); history.back();</script>';
        $this->show($script);
    }

    protected function ajaxError($msg, $errno=1) {
        $res = array(
            'result' => $errno,
            'message' => $msg,
            'data' => $_REQUEST
        );
        $this->ajaxReturn($res);
        exit;
    }
    protected function ajaxSuccess($data=null) {
        if (empty($data))
            $data = null;
        $res = array(
            'result' => 0,
            'message' => '',
            'data' => $data
        );
        $this->ajaxReturn($res);
        eixt;
    }

    protected function checkId($id) {
        if (empty($id)) {
            $this->error('访问的页面不存在');
        }
    }
    
    protected function logOperation($module, $op, $id, $title, $memo='') {
        $data['admid'] = $this->admin['uid'];
        $data['admname'] = $this->admin['username'];
        $data['module'] = $module;
        $data['optype'] = $op;
        $data['itemid'] = $id;
        $data['itemtitle'] = $title;
        $data['addtime'] = time();
        M('adminlog')->add($data);
    }
    
    protected function checkPerview($code) {
        $rules = $this->admin['rules'];
        //没有任何权限
        if (empty($rules)) 
            return false;
        //所有权限   
        if (is_string($rules) && $rules=='*') 
            return true;
        //单模块权限
        if (!in_array($code,$rules))
            return false;
        return true; 
    }
    
    protected function checkAccess() {
        if ($this->admin['username']=='admin')
            return true;
        //检测访问权限
        $controller = CONTROLLER_NAME;
        $action = ACTION_NAME;
        $ignoreArr = array('Misc','Home','Index','Profile','Message','Auth','Desktop');
        $viewArr = array('index','detail');
        $editArr = array('add','edit','save','update','delete','batch');
        
        //不需要验证权限的模块
        if (in_array($controller,$ignoreArr))
            return true;
        //Action权限分组
        if (!in_array($action, $editArr))
            $allowAction = 'view';
        else
            $allowAction = 'edit';
        $codeAll = $controller.'/*';
        $code = $controller.'/'.$allowAction;
        $roles = $this->admin['roleids'];
        $rules = $this->admin['rules'];
        //没有任何权限
        if (empty($rules)) 
            return false;
        $ignoreRules = array('Message/view');
        if (in_array($code, $ignoreRules))
            return true;
        //所有权限   
        if (is_string($rules) && $rules=='*') 
            return true;
        //模块所有权限
        if (in_array($codeAll,$rules)) 
            return true;
        //单模块权限
        if (!in_array($code,$rules))
            return false;
        return true; 
    }
    
    protected function storeUrl() {
		session('lasturl',get_url());
	}
	
	protected function getLastUrl() {
		$url = session('lasturl');
		if (empty($url)) {
			$url = __CONTROLLER__;
		}
		return $url;
	}
    
    protected function _initialize() {
        //检测是否登录
        $ignoreLogin = array('Auth');
        if (!in_array(CONTROLLER_NAME,$ignoreLogin)) {
            $this->admin = session('admin');
            if (empty($this->admin)) {
                $this->clientRedirect('Auth/index');
            }
        }
        $username = $this->admin['username'];
        if ($username == 'admin') {
            $this->assign('perview', array('delete'=> 1));
        } else {
            if (CONTROLLER_NAME == 'delete' || ($_REQUEST['op'] == 'delete')) {
                $this->error('您没有权限执行此操作，请与管理员联系');
            }
        }

        /*
        if (!$this->checkAccess()) {
            $this->error('您没有权限进行此操作，请与管理员联系');
        }
        */
        //初始化服务
        $this->cacheSvc = D('Cache','Service');
        $this->cacheSvc->initCache();
        $this->arctypeSvc = D('Arctype','Service');
        
        $admin = $this->admin;
        $this->assign('admininfo', $admin);
        $this->poweredby = C('SOFT_NAME').' '.C('SOFT_VERSION');
        $this->assign('pageTitle', $this->poweredby);
        
        //存储列表页url
		if (ACTION_NAME == 'index') {
			$this->storeUrl();
		}
    }

    protected function setResult($code, $data) {
        $json = json_encode($data);
        $script = '<script>parent.$.freebox.close(); parent.onResult("' . $code.'",' . $json .');</script>';
        $this->show($script);
    }


    protected function notify($uid,$title, $message, $param, $type='') {
        try {
            $model = service('Notify');
            $model->notify($uid,$title, $message, $param, $type);
        } catch (\Exception $e) {

        }

    }

    protected function success($msg, $url='', $sec = 0) {
        if (empty($url))
            $url = $this->getLastUrl();
        redirect($url);
    }
}