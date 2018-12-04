<?php
namespace Home\Controller;
class IndexController extends BaseController {
    public function index(){
    	$lang = cookie('think_language');
    	if(empty($lang)){
    		$lang=substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
		    if(preg_match("/zh-c/i",$lang)){
		        echo $lang;
		    }else if(preg_match("/zh/i",$lang)){
		        echo "繁体中文222";
		    }else if(preg_match("/en/i",$lang)){
		        echo "English333";
		    }else if(preg_match("/fr/i",$lang)){
		        echo "French";
		    }else if(preg_match("/de/i",$lang)){
		        echo "German";
		    }else if(preg_match("/jp/i",$lang)){
		        echo "Japanse";
		    }else if(preg_match("/ko/i",$lang)){
		        echo "Korean";
		    }else if(preg_match("/es/i",$lang)){
		        echo "Spanish";
		    }else if(preg_match("/sv/i",$lang)){
		        echo "Swedish";
		    }else{
		        echo $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		    }
    	}
        //显示模板
        $this->display();
    }
    
}