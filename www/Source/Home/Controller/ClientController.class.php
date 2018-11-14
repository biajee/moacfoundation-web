<?php
namespace Home\Controller;
class ClientController extends BaseController {
    public function index(){
        $code = 'client';
        $channel = $this->arctypeSvc->getType($code);
        $this->assign('channel', $channel);
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
