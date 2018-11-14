<?php
namespace Home\Controller;
class IndexController extends BaseController {
    public function index(){
    	$model = Service('Info');
        $page = 1;
        $count = 5;
        $start = ($page-1) * $count;
        $limit = "$start,$count";
        $order = 'istop DESC,id DESC';
        $list = $model->getInfos($where, $limit, $order);
        foreach($list as &$v) {
            if ($v['favnum']>99)
                $v['favstr'] = '99+';
            else
                $v['favstr'] = $v['favnum'];
            $v['timestr'] = timespan($v['addtime']);
            if (empty($v['image']))
                $v['image'] = $v['author']['avatar'];
        }
        $this->assign('list', $list);
        //显示模板
        $this->display();
    }
    
}