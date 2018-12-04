<?php
namespace Home\Controller;
class ResourcesController extends BaseController {
    public function resource(){
        //显示模板
        $this->display();
    }
    public function resource_news(){
    	$lang = $this->lang;
    	if($lang == 'zh-cn'){
    		$where['lang'] = array('neq',1);
    	}else{
    		$where['lang'] = array('neq',2);
    	}
    	$model = M('News');
    	$data = $model->where($where)->order('id desc')->select();
    	$this->assign('data',$data);
        //显示模板
        $this->display();
    }
    public function resource_events(){
    	$lang = $this->lang;
    	if($lang == 'zh-cn'){
    		$where['lang'] = array('neq',1);
    	}else{
    		$where['lang'] = array('neq',2);
    	}
    	$model = M('Events');
    	$data = $model->where($where)->order('id desc')->select();
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