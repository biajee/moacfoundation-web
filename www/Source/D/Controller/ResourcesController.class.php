<?php
namespace D\Controller;
class ResourcesController extends BaseController {
    public function resource_video(){
		$where1['lang'] = array('neq',2);//英文
		$where2['lang'] = array('neq',1);//中文
    	$model = M('Events');
    	$en = $model->where($where1)->order('id desc')->select();
    	$zh = $model->where($where2)->order('id desc')->select();
    	$this->assign('data1',$en);
    	$this->assign('data2',$zh);
        //显示模板
        $this->display();
    }
    public function resource_webinar(){
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
    
}