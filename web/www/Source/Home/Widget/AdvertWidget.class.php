<?php
namespace Home\Widget;
use Think\Controller;
class AdvertWidget extends Controller {
    //广告组
    public function group($code, $limit=10, $style='') {
        $svc = D('Advert', 'Service');
        $list = $svc->getAdvertsByType($code, $limit, 'sortno asc, id asc');
        $tpl = 'Widget/Advert/group';
        if (!empty($style))
            $tpl .= '_' . $style;
        $this->assign('list', $list);
        return $this->fetch($tpl);
    }

    //单个
    public function single($code, $style='') {
        $svc = D('Advert', 'Service');
        $data = $svc->getAdvert($code);
		if (empty($data['status']))
			return '';
        $tpl = 'Widget/Advert/single';
        if (empty($style)) {
            return $data['content'];
        } else {
            $this->assign('data', $data);
            if (!empty($style))
                $tpl .= '_' . $style;
            return $this->fetch($tpl);
        }
    }
    //内容广告
    public function content($code) {
        $model = M('Advert');
        $where['code'] = array('eq', $code);
        $data = $model->where("code='$code'")->getField('content');
        $data = html_entity_decode($data);
        return $data;
    }
    //微阅读
    public function weiread() {
        return $this->fetch('Widget/Advert/weiread');
    }
}