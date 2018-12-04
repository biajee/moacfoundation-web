<?php
namespace Wap\Controller;
class IndexController extends BaseController {
    public function _initialize()
    {
        parent::_initialize();
        $this->channel = 'home';
    }

    public function index() {
    	//模板输出
        $this->display();
    }
}