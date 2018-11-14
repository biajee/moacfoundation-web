<?php
namespace Home\Controller;
class EntrustController extends BaseController {
	public function index(){
		if (IS_POST) {
			$mobile = I('phone');
			$checkcode = I('checkcode');
			if (!$this->checkSmscode($checkcode, $mobile)) {
				$this->error('验证码错误');
			}
			$model = M('Entrust');
			$data = $model->create();
			$data['addtime'] = time();
			$model->add($data);
			$this->success('您已委托成功，稍后我们的经纪人会与您联系，请保持电话畅通。', U('Entrust/index'));
		} else {
			$this->crumbs[] = array('title'=>'二手房', 'url'=>'Resold/index');
			$this->crumbs[] = array('title'=>'一键委托', 'url'=>'');
			$this->page['channel'] = "resold";
			$this->page['title'] = '一键委托' . '-' . $this->page['title'];
			$this->assign('hash', $this->getHash());
			$this->display();
		}
	}
	
}