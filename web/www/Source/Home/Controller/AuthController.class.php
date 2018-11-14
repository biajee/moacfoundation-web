<?php 
	namespace Home\Controller;
	class AuthController extends  BaseController{
		protected function showError($message) {
			$this->assign('message', $message);
			$this->display('error');
			exit;
		}
		public function index(){
			$hash = $this->getHash();
			$this->assign('hash', $hash);
			$this->display();
		}
		
		public function broker(){
			$hash = $this->getHash();
			$this->assign('hash', $hash);
			$this->display();
		}
		
		public function login() {
			$model = D('Member', 'Service');
			$by = I('by');
			try {
				if (empty($by)) {
					$username = I('username');
					$password = I('password');
					$user = $model->login($username, $password);
				} else {
					$code = I('checkcode');
					$mobile = I('mobile');
					if ($this->checkCode($code)) {
						$user = $model->getMemberByMobile($mobile);
						if (empty($user)) {
							$uid = $model->mobileRegister($mobile);
							$user = $model->getMember($uid);		
						}
					} else {
						$this->showError('验证码错误');
					}
				}
			} catch (\Exception $e) {
				$this->showError($e->getMessage());
			}
			$sess = array(
				'uid' => $user['id'],
				'username' => $user['username'],
				'isbroker' => $user['isbroker']
			);
			session('member', $sess);
			if ($sess['isbroker']) {
				redirect(U('Broker/desktop'));
			} else
				redirect($this->refer);
		}
		
		public function logout() {
			session('member', null);
			$this->redirect('/');
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
		
		public function update() {
			if (empty($this->member)) {
				$this->error('请先登录或注册为会员');
			} else {
				if ($this->member['isbroker']==1) {
					$this->error('您已经是经纪人，无需升级');
				}
			}
			if (IS_POST) {
				$uid = $this->member['id'];
				$username = $this->member['username'];
				$password = I('password');
				$surname = I('surname');
				$mobile = I('mobile');
				$checkcode = I('checkcode');
				if (!$this->checkSmscode($checkcode, $mobile)) {
					$this->showError('验证码错误');
				}
				$vocation = I('vocation');
				$attention = I('attention');
				$user = array(
					'id' => $uid,
					'username' => $username,
					'surname' => $surname,
					'isbroker' => 1
				);
				D('Member','Service')->updateMember($user);
				$broker = array(
					'id' => $uid,
					'username'=>$username,
					'surname' => $surname,
					'vocation'=>$vocation,
					'attention'=> $attention
				);
				D('Broker','Service')->addBroker($broker);
				$this->success('升级成功', U('Broker/desktop'));
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
	}