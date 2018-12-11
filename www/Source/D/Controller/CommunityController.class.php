<?php
namespace D\Controller;
class CommunityController extends BaseController {
    public function community(){
    	$lang = cookie('think_lang');
//  	var_dump($lang);exit;
        //显示模板
        $this->display();
    }
    
}