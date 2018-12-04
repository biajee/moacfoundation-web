<?php
namespace Home\Controller;
class PublicController extends BaseController {
    public function error(){
        //显示模板
        $this->display();
    }
    public function success(){
        //显示模板
        $this->display();
    }
    
}