<?php
namespace Home\Controller;
class ContactController extends BaseController {
    public function index(){
        $code = 'contact';
        $channel = $this->arctypeSvc->getType($code);
        $cid = I('cid');
        if ($cid) {
            $this->checkId($cid);
            $type = $channel;
        } else {
            $type = $channel;
            $cid = $type['id'];
        }
        $this->crumbs[] = array('title'=>$type['title'], 'url'=>'');
        $this->page['channel'] = $code;
        $this->page['title'] = $type['title'] . '-' . $this->page['title'];
        $this->assign('data', $type);
        $this->display();
    }
}
