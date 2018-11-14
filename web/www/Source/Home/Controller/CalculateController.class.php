<?php
namespace Home\Controller;
class CalculateController extends BaseController {
	public function index(){
		$this->crumbs[] = array('title'=>'新房', 'url'=>'Building/index');
		$this->crumbs[] = array('title'=>'房贷计算器', 'url'=>'');
		$this->page['channel'] = "building";
		$this->page['title'] = '房贷计算器' . '-' . $this->page['title'];
		$this->display();
	}
	public function fund(){
		$this->crumbs[] = array('title'=>'新房', 'url'=>'Building/index');
		$this->crumbs[] = array('title'=>'房贷计算器', 'url'=>'');
		$this->page['channel'] = "building";
		$this->page['title'] = '房贷计算器' . '-' . $this->page['title'];
		$this->display();
	}
	public function combine(){
		$this->crumbs[] = array('title'=>'新房', 'url'=>'Building/index');
		$this->crumbs[] = array('title'=>'房贷计算器', 'url'=>'');
		$this->page['channel'] = "building";
		$this->page['title'] = '房贷计算器' . '-' . $this->page['title'];
		$this->display();
	}
}