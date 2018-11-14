<?php
namespace Home\Controller;
class NewsController extends BaseController {
    protected $code = 'news';
    public function index(){
        $module = I('module');
        if (empty($module))
            $module = $_GET['module'];
        if (!empty($module))
            $where['module'] = array('eq', $module);
        $model = Service('Info');
        $page = 1;
        $count = 12;
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
        $this->assign('view', $view);
        $this->assign('module', $module);
        $this->assign('list', $list);
//      var_dump($list);exit;
        $this->display();
    }
    public function details() {
    	$id = I('id');
        $model = service('Info');
        $memModel = service('Member');
        $data = $model->getInfo($id);
        $cond['catid'] = 5;
        $cond['key'] = $data['module'];
        $data['is_goods'] = $memModel->getIsgoods($cond,'is_goods');
        //类型功能信息
        $listcond = array(
        	'key' => $data['module'],
        	'catid' => '5'
        );
        $listitem = M('listitem')
        			->join('edb_action ON edb_listitem.actid=edb_action.id')
        			->where($listcond)->find();
        //是否收藏
        $uid = $this->member['id'];
        if ($uid) {
            $favModel = service('Favorite');
            $cnt = $favModel->getFavoriteCount(array(
                'memid' => array('eq', $uid),
                'module' => array('eq','info'),
                'itemid' => array('eq', $id)
            ));
            $data['collected'] = $cnt > 0;
        } else {
            $data['collected'] = false;
        }
		$this->assign('langSet', cookie('think_language'));
        $this->assign('data', $data);
        $this->assign('list', $listitem);
        $caption = L('info_'.$data['module']) . L('info_detail');
        $author = $memModel->getMember($data['memid']);
        $this->assign('uid', $uid);
        $this->assign('author', $author);
        $this->assign('caption', $caption);
        $this->page['title'] = $data['title'];
        $this->share['text'] = $data['title'] . '@' . L('site_name');
//      var_dump($data);exit;
        $this->display();
    }

	public function lists(){
        $channel = $this->arctypeSvc->getType($this->code);
            $this->crumbs[] = array('title'=>$channel['title'], 'url'=>'');
        $cid = I('cid');
        if ($cid) {
            $this->checkId($cid);
            $type = $this->arctypeSvc->getType($cid);
			$title = $type['title'];
        } else {
            $type = $channel;
            //$cid = $type['id'];
			$title = '全部新闻';
        }

        $model = M('Article');
        $where = array();
		$typecode = "";
        if ($cid) {
            $where['catid'] = array('eq',$cid);
			$data = M('Arctype')->find($cid);
			$typecode = $data['code'];
        }
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$where['title'] = array('like', "%{$keyword}%");
			$title = $keyword.' 搜索结果';
		}
        $field = 'id,title,image,memo,addtime';
        $pagesize = 10;
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

        $this->crumbs[] = array('title'=>$title, 'url'=>'');
        $this->page['channel'] = $this->code;
        $this->page['title'] = $title . '-' . $this->page['title'];
        $this->assign('type', $type);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
		$this->assign("typecode",$typecode);
        $this->display();
    }

    public function ajax() {
        $articleSvc = D('Article','Service');
        $cid = I('cid');
        $page = I('page');
        $pagesize = 8;
        $where['catid'] = array('eq', $cid);
        $order = "id DESC";
        $limit = ($page-1)*$pagesize .','.$pagesize;
        $list = $articleSvc->getArticles($where, $limit, $order);
        foreach($list as $k=>$v) {
            $list[$k]['url'] = U(ucfirst($this->code).'/detail/'.$v['id']);
        }
        $this->assign('list', $list);
        $this->display();
    }
    public function detail() {
        $channel = $this->arctypeSvc->getType($this->code);
        $id = I('get.id');
        $this->checkId($id);
        $model = D('Article', 'Service');
        $data = $model->getArticle($id);
        if ($data) {
            $type =  $this->arctypeSvc->getType($data['catid']);
            $this->assign('type', $type);
        }
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $this->crumbs[] = array('title'=>$type['title'], 'url'=>$type['url']);
        $this->crumbs[] = array('title'=>'正文', 'url'=>'');
        $this->page['channel']= $this->code;
        $this->page['title'] = $data['title'] . '-' . $this->page['title'];
        $this->assign('data', $data);
        /*热门新闻*/
        $where = array(
			'id' => array('neq', $id),
			'catid' => array('eq', $data['catid'])
		);
		$order = 'id desc';
        $hotnews = $model->getArticles($where, 3, $order);
        $this->assign('hotNewsList', $hotnews);
        $this->display();
    }

}
