<?php
namespace Home\Controller;
class ErrorController extends BaseController {
	public function index(){
		$this->assign('waitSecond',3);
		$this->display();
	}
}