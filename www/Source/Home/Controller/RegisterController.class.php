<?php 
	namespace Home\Controller;
	class RegisterController extends  BaseController{
		
		protected function showError($message) {
			$this->assign('message', $message);
			$this->display('error');
			exit;
		}
		public function index(){
			if (IS_POST) {
				$username = I('username');
				$password = I('password');
				$repassword = I('repassword');
				$mobile = I('mobile');
				$checkcode = I('checkcode');
				if (!$this->checkSmscode($checkcode, $mobile))
					$this->showError('验证码错误');
				$data = array(
					'username' => $username,
					'password' => $password,
					'mobile' => $mobile
				);
				$model = D('Member','Service');
				try{
					$uid = $model->register($data);
					$this->assign('data', $data);
					$this->display('success');
				} catch(\Exception $e) {
					$this->showError($e->getMessage());
				}
			} else {
				$hash = $this->getHash();
				$this->assign('hash', $hash);
				$this->display();
			}	
		}
		public function broker(){
			if (IS_POST) {
				$username = I('username');
				$password = I('password');
				$repassword = I('repassword');
				$surname = I('surname');
				$mobile = I('mobile');
				$checkcode = I('checkcode');
				$vocation = I('vocation');
				$attention = I('attention');
				if (!$this->checkSmscode($checkcode, $mobile))
					$this->showError('验证码错误');
				$data = array(
					'username' => $username,
					'password' => $password,
					'mobile' => $mobile,
					'surname' => $surname,
					'vocation' => $vocation,
					'attention' => $attention
				);
				$model = D('Broker','Service');
				try{
					$uid = $model->register($data);
					$data = $model->getMember($uid);
					$where = array(
						'isbroker' => array('eq', 1)
					);
					$cnt = $model->getMemberCount($where);
					$data['order'] = $cnt;
					$this->assign('data', $data);
					$this->display('success2');
				} catch(\Exception $e) {
					$this->showError($e->getMessage());
				}
			} else {
				$district = $this->cacheSvc->getData('DistrictMlt');
				$vocation = $this->cacheSvc->getData('BrokerVocationMlt');
			
				$this->assign('districtList', $district);
				$this->assign('vocationList', $vocation);
				$hash = $this->getHash();
				$this->assign('hash', $hash);
				$this->display();
			}	
		}
		public function validname() {
			$username = I('param');
			$model = M('Member');
			$cnt = $model->where("username='$username'")->count();
			if ($cnt>0)
				$data = array('info'=>'用户名已被注册','status'=>'n');
			else
				$data = array('info'=>'','status'=>'y');
			$this->ajaxReturn($data);    
		}
		
		public function validmobile() {
			$mobile = I('param');
			$model = M('Member');
			$cnt = $model->where("mobile='$mobile'")->count();
			if ($cnt>0)
				$data = array('info'=>'手机号已注册','status'=>'n');
			else
				$data = array('info'=>'','status'=>'y');
			$this->ajaxReturn($data);    
		}
		
		public function validcode() {
			$checkcode = I('param');
			if (!$this->checkSmscode($checkcode))
				$data = array('info'=>'验证码错误','status'=>'n');
			else
				$data = array('info'=>'','status'=>'y');
			$this->ajaxReturn($data);    
		}
	}