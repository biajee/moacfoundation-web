<?php
namespace Dm\Controller;
class ResourcesController extends BaseController {
    public function resource_news(){
    	$model = M('News');
    	$data = $model->where()->select();
    	$this->assign('data',$data);
        //显示模板
        $this->display();
    }
    public function resource_events(){
    	$model = M('Events');
    	$data = $model->where()->select();
    	$this->assign('data',$data);
        //显示模板
        $this->display();
    }
    public function resource_video(){
        //显示模板
        $this->display();
    }
    public function resource_webinar(){
        //显示模板
        $this->display();
    }
    
}