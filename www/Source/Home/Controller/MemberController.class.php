<?php 
	namespace Home\Controller;
	class MemberController extends  BaseController{
		protected function _initialize() {
			parent::_initialize();
			//检测是否登录
			$this->checkAuth();
			$stats = array();
			$uid = $this->member['id'];
			$mobile = $this->member['mobile'];
			$stats['message'] = M('Message')->where(array('toid'=>array('eq', $uid)))->count();
			$stats['tour'] = M('Tourapply')->where(array('phone'=>array('eq', $mobile)))->count();
			$stats['resold'] = M('Resold')->where(array('memid'=>array('eq', $uid)))->count();
			$this->assign('stats', $stats);
			$today = time();
			$calendar = array (
				'day' => date('d', $today),
				'month' => date('Y.m', $today)
			);
			$week = date('w', $today);
			$weekArr = array('日','一','二','三','四','五','六');
			$calendar['week'] = '星期' . $weekArr[$week];
			$this->assign('calendar', $calendar);
		}
		public function index() {
			$this->redirect('message');
		}
		
		public function message() {
			$uid = $this->member['id'];
			$where = array(
				'toid' => array('eq', $uid)
			);
			$model = M('Message');
			$pagesize = 10;
			$count = $model->where($where)->count();
			$pager = $pager = new \Think\Page($count, $pagesize);
			$multi = $pager->show();
			$order = 'id DESC';
			$list = $model->where($where)->limit($pager->firstRow.','.$pager->listRows)->order($order)->select();
			$this->assign('multi', $multi);
			$this->assign('list', $list);
			$this->page['title'] = '我的消息' . '-' . $this->page['title'];
			$this->assign('sidenav', 'message');
			$this->display();
		}
		public function resold() {
			$uid = $this->member['id'];
			$where = array(
				'memid' => array('eq', $uid)
			);
			$model = M('Resold');
			$pagesize = 10;
			$count = $model->where($where)->count();
			$pager = $pager = new \Think\Page($count, $pagesize);
			$multi = $pager->show();
			$order = 'id DESC';
			$list = $model->where($where)->limit($pager->firstRow.','.$pager->listRows)->order($order)->select();
			$this->assign('multi', $multi);
			$this->assign('list', $list);
			$this->page['title'] = '我的发布' . '-' . $this->page['title'];
			$this->assign('sidenav', 'resold');
			$this->display();
		}
		public function tour() {
			$uid = $this->member['id'];
			$mobile = $this->member['mobile'];
			$where = array(
				'phone' => array('eq', $mobile)
			);
			$model = M('Tourapply');
			$pagesize = 10;
			$count = $model->where($where)->count();
			$pager = $pager = new \Think\Page($count, $pagesize);
			$multi = $pager->show();
			$order = 'id DESC';
			$list = $model->where($where)->limit($pager->firstRow.','.$pager->listRows)->order($order)->select();
			$this->assign('multi', $multi);
			$this->assign('list', $list);
			$this->page['title'] = '我的看房' . '-' . $this->page['title'];
			$this->assign('sidenav', 'tour');
			$this->display();
		}
		
		public function profile() {
			$uid = $this->member['uid'];			
			if (IS_POST) {
				$data = array(
					'id' => $uid,
					'avatar' => I('avatar'),
					'nickname' => I('nickname'),
					'surname' => I('surname'),
					'gender' => I('gender'),
					'age' => I('age'),
					'phone' => I('phone')
				);
				try {
					D('Member','Service')->updateMember($data);
					$this->success('更新成功', U('Member/profile'));
				} catch (\Exception $e) {
					$this->error($e->getMessage());
				}
			} else {
				$data = D('Member', 'Service')->getMember($uid);
				$this->assign('data', $data);
				$this->page['title'] = '个人资料' . '-' . $this->page['title'];
				$this->assign('sidenav', 'profile');
				$this->display();
			}
			
		}
		
		public function setting() {
			if (IS_POST) {
				$uid = $this->member['id'];
				$username = $this->member['username'];
				$model = D('Member', 'Service');
				$by = I('by');
				if (empty($by)) { //普通账号
					$oldpassword = I('oldpassword');
					$password = I('password');
					$oldpassword = pass_encode($username, $oldpassword);
					$where = array(
						'id' => array('eq', $uid),
						'password'=>array('eq', $oldpassword)
					);
					$data = $model->getMember($where);
					
					if (empty($data)) {
						$this->error('旧密码错误');
					} else {
						$data = array(
							'id' => $uid,
							'username' => $username,
							'password' => $password
						);
						$model->updateMember($data);
						$this->success('密码修改成功',U('Member/setting'));
					}
				} else { //手机动态码修改
					$checkcode = I('checkcode');
					$smscode = session('smscode');
					$password = I('password');
					if (!empty($checkcode) || $checkcode == $smscode) {
						$data = array(
							'id' => $uid,
							'username' => $username,
							'password' => $password
						);
						$model->updateMember($data);
						$this->success('密码修改成功',U('Member/setting'));
						
					} else {
						$this->error('验证码错误');
					}
					
				}
			} else {
				$this->page['title'] = '安全设置' . '-' . $this->page['title'];
				$this->assign('sidenav', 'setting');
				$this->display();
			}
			
		}
	}