<?php
namespace Home\Controller;
class IndexController extends BaseController {
    public function index(){
    	$lang = cookie('think_lang');
//  	var_dump($lang);exit;
        //显示模板
        $this->display();
    }
    
}