<?php
namespace Home\Controller;
class FinanceController extends BaseController {
	protected $code = 'finance';
	public function index(){
		$this->page['channel'] = $this->code;
        $this->page['title'] = '金融-' . $this->page['title'];
		$this->display();
	}
}