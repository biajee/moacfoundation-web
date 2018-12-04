<?php
namespace Wap\Controller;
class ResourcesController extends BaseController {
    public function resource(){
        //显示模板
        $this->display();
    }
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
    public function resource_Whitepaper(){
        //显示模板
        $this->display();
    }
    public function resource_MainNet(){
        //显示模板
        $this->display();
    }
    public function resource_TestNet(){
        //显示模板
        $this->display();
    }
    public function resource_MoacComparison(){
        //显示模板
        $this->display();
    }
    
}