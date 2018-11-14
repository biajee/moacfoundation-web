<?php
namespace Home\Widget;
use Think\Controller;
class BuildingWidget extends Controller {
    //热门
    public function hot($limit, $style='') {
        $buildingSvc = D('Building','Service');
        $where = array(
            'ishot'=>array('gt',0)
        );
        $order = 'ishot DESC';
        $list = $buildingSvc->getBuildings($where, $limit, $order);
        $this->assign('list', $list);
        $tpl = 'Widget/Building/hot';
        if ($style)
            $tpl .= '_'.$style;
        return $this->fetch($tpl);
    }
    //评测
    public function review($limit) {
        $buildingSvc = D('Building','Service');
        $where = array(
            'isreview'=>array('gt',0)
        );
        $order = 'isreview DESC';
        $list = $buildingSvc->getBuildings($where, $limit, $order);
        $this->assign('list', $list);
        return $this->fetch('Widget/Building/review');
    }
    
    public function tour($limit) {
        //看房团
        $tourSvc = D('Tour', 'Service');
        $tour = $tourSvc->getTour($limit);
        foreach($tour['items'] as $k=>$v) {
            $tour['items'][$k]['url'] = U('Building/detail/'.$v['id']);
        }
        $this->assign('tour', $tour);
        return $this->fetch('Widget/Building/tour');
    }

    public function ulike($limit) {
        $model = D('Building','Service');
        //猜你喜欢
        $list = $model->getUlike($limit);
        $this->assign('list', $list);
		return $this->fetch('Widget/Building/ulike');
    }

    
}