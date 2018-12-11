<?php
namespace Wap\Controller;
use Think\Controller;
class BaseController extends Controller {
	protected $cacheSvc = null;
    protected $member = null;
    protected $page = array();
    protected $cache = null;
    protected $channel = '';
    protected $setting = null;
    protected $share = null;
    protected $needshare = false;
    
    public function _initialize() {
    	$pageURL = $_SERVER["REQUEST_URI"];
//  	$pageURL = 'http';
//		$pageURL .= "://";
//		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		$this->assign('pageURL', $pageURL);
        $this->cache = service('Cache');
        //系统设置
        $setting = $this->cache->getData('SettingMap');
        $this->setting = array_change_key_case($setting,  CASE_LOWER);
        C($this->setting);
        $this->assign('setting', $this->setting);
        unset($setting);
        //会员信息
        //$sess = session('member');
        $sess = $this->getSess();
        if (!empty($sess)) {
            $user = D('Member', 'Service')->getMember($sess['id']);
            $user['uid'] = $user['id'];
            $this->member = $user;

            $this->assign('member', $this->member);
        }
        // Share
        $this->buildShare();
        //语言包功能
        $this->lang = cookie('think_language');
    	if(empty($this->lang)){
    		$lang=substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
		    if(preg_match("/zh-c/i",$lang)){
		        $this->lang = 'zh-cn';
		    }else if(preg_match("/zh/i",$lang)){
		        $this->lang = 'zh-cn';
		    }else if(preg_match("/en/i",$lang)){
		        $this->lang = 'en-us';
		    }else if(preg_match("/fr/i",$lang)){
		        $this->lang = 'en-us';
		    }else if(preg_match("/de/i",$lang)){
		        $this->lang = 'en-us';
		    }else if(preg_match("/jp/i",$lang)){
		        $this->lang = 'en-us';
		    }else if(preg_match("/ko/i",$lang)){
		        $this->lang = 'en-us';
		    }else if(preg_match("/es/i",$lang)){
		        $this->lang = 'en-us';
		    }else if(preg_match("/sv/i",$lang)){
		        $this->lang = 'en-us';
		    }else{
		        $this->lang = 'en-us';
		    }
    	}
    	$this->assign('langset', $this->lang);
        //初始化服务
        $this->cacheSvc = D('Cache','Service');

        //Referee
        $referee = I('tn');
        if ($referee) {
            session('referee', $referee);
        }
        //Wechat share
        if (is_weixin()) {
            $svc = service('Wechat');
            //$svc->clearCache();
            $package = $svc->getSignPackage();
            $this->assign('isWeixin', true);
            $this->assign('signPackage', $package);
        }
        $this->getBadge();
        $this->page = array(
            'title' => $this->setting['site_seotitle'] . '-'. $this->setting['site_name'],
            'keywords' => $this->setting['site_seokey'],
            'description' => $this->setting['site_seodesc']
        );

        //
        if (empty($this->member)) {
            $refer = $_SERVER['REQUEST_URI'];
            $url = U('Auth/index') . '?refer=' . urlencode($refer);
            $this->assign('loginUrl', $url);
        }
    }

    public function _empty() {
        $this->show('栏目建设中...');
    }
    // 检查id安全性，防止注入
    public function checkId($id) {
        if (!is_numeric($id)) {
            $this->error(L('error_http_404'));
        }
    }
    public function checkAuth() {

        if (empty($this->member)) {
            $lang = L('login_before_action');
            if (IS_AJAX) {
               $this->ajaxError($lang, 2);
            } else {
                $refer = $_SERVER['REQUEST_URI'];
                $url = U('Auth/index') . '?refer=' . urlencode($refer);
                redirect($url);
            }
        }
    }

    /**
     * 检查提交内容中的敏感词
     * @param $text 检查的字符串
     * @return bool
     */
    public function checkText($text) {
        if (!sensor_test($text))
            return false;
        return true;
    }

    /**
     * Ajax返回错误信息
     * @param $msg 消息
     * @param int $errno 错误代码，可选
     */
    protected function ajaxError($msg, $errno=1) {
        $res = array(
            'result' => $errno,
            'message' => $msg,
            'data' => $_REQUEST
        );
        $this->ajaxReturn($res);
        exit;
    }

    /**
     * Ajax返回成功信息
     * @param null $data 数据
     * @param string $msg 消息
     */
    protected function ajaxSuccess($data=null, $msg='') {
        if (empty($data))
            $data = null;
        $res = array(
            'result' => 0,
            'message' => $msg,
            'data' => $data
        );
        $this->ajaxReturn($res);
        eixt;
    }

    protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        $this->assign('channel', $this->channel);
        $this->assign('page', $this->page);
        $this->assign('needshare', $this->needshare);
        $this->assign('share', $this->share);
        $this->assign('lang', array_change_key_case(L(), CASE_LOWER));
        parent::display($templateFile,$charset,$contentType,$content,$prefix);
    }

    protected function getPubStat($uid) {
        $where = array(
            'memid' => array('eq', $uid),
            'module' => array('eq', 'service')
        );
        $stat['service'] = M('Info')->where($where)->count();
        $where['task'][1] = 'taks';
        $stat['task'] = M('Info')->where($where)->count();
        $where['task'][1] = 'taks';
        $stat['news'] = M('Info')->where($where)->count();
        return $stat;
    }

    /**
     * 获取导航栏红点提示数字
     */
    protected function getBadge() {
        if ($this->member) {
            $badge = array();
            $uid = $this->member['id'];
            $model = service('Chat');
            $badge['message'] = $model->getMemStat($uid);
            $this->assign('badge', $badge);
        }
    }
    public function buildShare($text) {
        $this->share = array(
            'url' => $this->getShareUrl(),
            'text' => L('site_name')
        );

    }
    protected function getShareUrl() {
        $url = get_url();
        if ($this->member) {
            $uid = $this->member['uid'];
            if (strpos($url, '?') !== FALSE) {
                $url .= '&tn=' . $uid;
            } else {
                $url .= '?tn='. $uid;
            }
        }

        return $url;
    }
    protected function notify($uid,$title, $message, $param, $type) {
        try {
            $model = service('Notify');
            $model->notify($uid,$title, $message, $param, $type);
        } catch (\Exception $e) {

        }

    }

    protected function success($msg, $url='', $sec=0) {
        redirect($url);
    }

    protected function setSess($sess) {
        $code = serialize($sess);
        $code = authcode($code, 'ENCODE');
        $expire = C('MEMBER_SESSIONEXPIRE', null, 7);
        cookie('xms', $code, 3600*24*$expire);
    }

    protected function getSess() {
        $code = cookie('xms');
        if ($code) {
            $code = authcode($code, 'DECODE');
            $sess = unserialize($code);
            return $sess;
        } else {
            return false;
        }
    }

    protected function clearSess() {
        cookie('xms', null);
    }
}