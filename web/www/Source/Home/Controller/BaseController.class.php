<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    protected $cacheSvc = null;
    protected $arctypeSvc = null;
    protected $page = array();
    protected $crumbs = array();
    protected $setting = array();
    protected $member = array();
    protected $refer = '';
    protected function _initialize() {
        //会员信息
        $sess = session('member');
        if (!empty($sess)) {
            $user = D('Member', 'Service')->getMember($sess['uid']);
            $user['uid'] = $user['id'];
            $this->member = $user;
            
            $this->assign('member', $this->member);
        }
       $referee = $_REQUEST['referee'];
        if ($referee) {
            session('referee', $referee);
        }
        $this->refer = $this->getRefer();
        $this->assign('refer', $this->refer);
        //初始化工作
        $this->cacheSvc = D('Cache','Service');
        $this->arctypeSvc = D('Arctype','Service');
        //系统设置
        $this->setting = $this->cacheSvc->getData('SettingMap');
        C($this->setting);
        $this->assign('setting', $this->setting);
        //初始化页面相关
        $this->page['title'] = $this->setting['SITE_NAME'];
        $this->page['keywords'] = $this->setting['SITE_SEOKEY'];
        $this->page['desc'] = $this->setting['SITE_SEODESC'];
        $this->page['channel'] = 'home';
        $this->crumbs[] = array('title'=>'首页','url'=>'/');
        //获取栏目列表
        $list = $this->arctypeSvc->getChildren(0,'menu');
        $this->assign('navbar', $list);
        unset($list);
        //首页大图
          $advSvc = D('Advert', 'Service');
         $adbigdata = $advSvc->getAdvert('A1');//首页大图
        $this->assign('adbig', $adbigdata);
        //轮播
        /*$advSvc = D('Advert', 'Service');
        $list = $advSvc->getAdverts();
        $this->assign('homeflash', $list);
        unset($list); */
        //友情链接
        $list = M('friendlink')->select();
        $this->assign('friendlink', $list);
        unset($list);
    }
    public function _empty() {
        $this->redirect('Error/index');
    }
    //检查id安全性，防止注入
    public function checkId($id) {
    	if (!is_numeric($id)) {
    		$this->error('页面不存在或已被管理员删除');
    	}
    }
    public function checkAuth() {
        
        if (empty($this->member)) {
            $refer = $_SERVER['REQUEST_URI'];
            $url = U('Auth/index') .'?refer='.urlencode($refer);
            $this->error('请先登录后操作',$url);
        }
    }
    protected function getRefer() {
        $refer = I('refer');
        if (empty($refer))
            $refer = $_SERVER['HTTP_REFERER'];
        if (empty($refer))
            $refer = U('/');
        return $refer;
    }
    protected function getHash() {
        $hash = uniqid2();
        session('formhash', $hash);
        return $hash;
    }
    
    protected function checkHash($str) {
        $hash = session('formhash');
        if (empty($str) || empty($hash))
            return false;
        if ($str == $hash) {
            return true;
        } else {
            return false;
        }
    }
	
    protected function checkSmscode($code, $mobile='') {
		if (empty($mobile))
			$mobile = session('lastmobile');
		$smscode = session('smscode/'.$mobile);
		if (!empty($code) && $smscode == $code) {
			return true;
		} else {
			return false;
		}
	}
    public function mobileLogin($mobile) {
        $smscode = session('smscode');
        $checkcode = I('checkcode');
        if (empty($checkcode) || $checkcode != $smscode)
            E('验证码错误');
        $memSvc = D('Member', 'Service');
        $user = $memSvc->getMemberByMobile($mobile); //是否注册
        if (empty($user)) {
            $uid = $memSvc->mobileRegister($mobile);
            $user = $memSvc->getMember($uid);
            $sess = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'isbroker' => $user['isbroker']
            );
            session('member', $sess);
            $this->member = $sess;
        }

    }

    public function checkBroker() {
        if (empty($this->member) || empty($this->member['isbroker'])) {
            $this->error('您还不是经纪人', U('Auth/broker'));
        }
    }
    protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        $this->assign('crumbs', $this->crumbs);
    	$this->assign('page', $this->page);
    	parent::display($templateFile,$charset,$contentType,$content,$prefix);
    }
    
}