<?php
namespace Home\Controller;
class BlogController extends BaseController {
    protected $code = 'blog';
    public function index(){
        $channel = $this->arctypeSvc->getType($this->code);
        $cid = I('cid');
        if ($cid) {
            $this->checkId($cid);
            $type = $this->arctypeSvc->getType($cid);
            $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
            $this->crumbs[] = array('title'=>$type['title'], 'url'=>'');
        } else {
            $type = $channel;
            //$cid = $type['id'];
            $this->crumbs[] = array('title'=>$channel['title'], 'url'=>'');
        }
        
        $model = M('Article');
        
        $where = array();
        if ($cid) {
            $where['catid'] = array('eq',$cid);
        } else {
            $ids = $this->arctypeSvc->getLeaves($type['id']);
            $where['catid'] = array('in',$ids);  
        }
        
        $field = 'id,title,image,memo,addtime';
        $pagesize = 16;
        $order = "id DESC";
        $count = $model->where($where)->count();
        $pager = $pager = new \Think\Page($count, $pagesize);
        $multi = $pager->show();
        $list = $model->field($field)
                            ->where($where)
                            ->order($order)
                            ->limit($pager->firstRow.','.$pager->listRows)
                            ->select();
        foreach($list as $k=>$v) {
            $list[$k]['url'] = U(ucfirst($this->code).'/detail?id='.$v['id']);
        }
        $this->page['channel'] = $this->code;
        $this->page['title'] = $type['title'] . '-' . $this->page['title'];
        $this->assign('type', $type);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }

    public function detail() {
        $id = I('get.id');
        $this->checkId($id);
        $model = M('Article');
        $data = $model->find($id);
        if ($data) {
            $type =  $this->arctypeSvc->getType($data['catid']);
            $this->assign('type', $type);
        }
            $this->crumbs[] = array('title'=>$type['title'], 'url'=>$type['url']);
            $this->crumbs[] = array('title'=>'详情', 'url'=>'');
        $this->page['channel']= $this->code;
        $this->page['title'] = $data['title'] . '-' . $this->page['title'];
        $this->assign('data', $data);
        $this->display();
    }
}
