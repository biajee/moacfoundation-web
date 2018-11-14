<?php
namespace Home\Widget;
use Think\Controller;
class ArctypeWidget extends Controller {
    public function channel($code, $nowid=0) {
        $arctypeSvc = D('Arctype','Service');
        $data = $arctypeSvc->getType($code);
        $list = $arctypeSvc->getChildren($data['id']);
        $this->assign('data', $data);
        $this->assign('list', $list);
        $this->assign('nowid', $nowid);
        return $this->fetch('Widget/channel');
    }
}