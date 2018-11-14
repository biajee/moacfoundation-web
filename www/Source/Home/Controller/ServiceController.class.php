<?php
namespace Home\Controller;
class ServiceController extends BaseController {
    public function index(){
        $code = 'service';
        $channel = $this->arctypeSvc->getType($code);
        $cid = I('cid');
        if ($cid) {
            $this->checkId($cid);
            $type = $this->arctypeSvc->getType($cid);
        } else {
            $type = $this->arctypeSvc->getFirstChild($channel['id']);
            $cid = $type['id'];
        }
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $this->crumbs[] = array('title'=>$type['title'], 'url'=>'');

        $this->page['channel'] = $code;
        $this->page['title'] = $type['title'] . '-' . $this->page['title'];
        $this->assign('data', $type);
        $this->display();
    }
}
