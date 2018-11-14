<?php
namespace Home\Controller;
class BrokerController extends BaseController {
    protected  $code = 'broker';
	
	protected function _initialize() {
		parent::_initialize();
		if (empty($this->member) || $this->member['isbroker'] == 0 ) {
			if (ACTION_NAME != 'register')
				$this->redirect('register');
		}
	}
    public function index(){
		
        $code = 'broker';
        $channel = $this->arctypeSvc->getType($code);

        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $this->crumbs[] = array('title'=>'推荐流程', 'url'=>'');

        $this->page['channel'] = $code;
        $this->page['title'] = '推荐流程' . '-' . $this->page['title'];
        $this->display();
    }
    public function building() {
        $channel = $this->arctypeSvc->getType($this->code);
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $buildingSvc = D('Building','Service');
        $filters = array(
            array(
                'district' => array('code'=>'District','title'=>'区域'),
                'subway' => array('code'=>'Subway','title'=>'地铁'),
                'price' => array('code'=>'BuildingPrice','title'=>'房价'),
                'area' => array('code'=>'BuildingArea','title'=>'面积'),
                'btype' => array('code'=>'BuildingType','title'=>'类型'),
                'feature' => array('code'=>'BuildingFeature','title'=>'楼盘特色'),
               /* 'tag' => array('code'=>'BuildingTag','title'=>'楼盘标签')*/
            ),
            array(
                'layout' => array('code'=>'HouseLayout','title'=>'户型'),
                'district' => array('code'=>'District','title'=>'区域'),
                'subway' => array('code'=>'Subway','title'=>'地铁'),
                'htype' => array('code'=>'HouseType','title'=>'类型'),
                //'amount' => array('code'=>'LayoutAmount','title'=>'价格')
            )
        );
        $tab = I('tab');
        if (empty($tab))
            $tab = 0;
        $filterList = $filters[$tab];
        foreach( $filterList as $key => $v) {
            $k = $v['code'].'Mlt';
            $filterList[$key]['key'] = $key;
            $filterList[$key]['items'] = $this->cacheSvc->getData($k);
        }
        $this->assign('filterList', $filterList);
		 //搜索栏右侧楼盘特色
	    $featureList = $this->cacheSvc->getData("BuildingFeatureMlt");
       $this->assign("featureList",$featureList);
        //查询条件
        $where = array(
            'brokerage' => array('neq','')
        );
        $selector = array();
        //关键字
        $keyword = I('keyword');
        if (!empty($keyword))
            $where['title'] = array('like', "%$keyword%");
        //地区
        $code = 'district';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['district'] = array('eq', $item['title']);
            $selector[$code] = $item;
        }
        //价格区间
        $code = 'price';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where['price'] = parse_range($range);
            $selector[$code] = $item;
        }
        //面积区间
        $code = 'area';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where2 = array(
                'grossarea' => parse_range($range)
            );
            $subsql = M('Buildinglayout')->field('buildingid')->where($where2)->buildSql();
            $where['id'] = array('exp', "IN$subsql");
            $selector[$code] = $item;
        }
        //总价区间
        $code = 'amount';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where2 = array(
                'amount' => parse_range($range)
            );
            $subsql = M('Buildinglayout')->field('buildingid')->where($where2)->buildSql();
            $where['id'] = array('exp', "IN$subsql");
            $selector[$code] = $item;
        }
        //户型
        $code = 'layout';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where2 = array();
            if ($range[0] == $range[1])

                $where2 = array(
                    'bedroom' => parse_range($range)
                );
            $subsql = M('Buildinglayout')->field('buildingid')->where($where2)->buildSql();
            $where['id'] = array('exp', "IN$subsql");
            $selector[$code] = $item;
        }
        //楼盘类型
        $code = 'btype';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['buildingtype'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //房屋类型
        $code = 'htype';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['housetype'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //地铁
        $code = 'subway';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['subway'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //特色
        $code = 'feature';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['feature'] = array('like', '% ' . $item['title'] . ' %');
           /* $selector[$code] = $item;*/
        }
        //标签
        $code = 'tag';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['tags'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //环线
        $code = 'circuit';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['feature'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        $this->assign('selectorList', $selector);
        //统计
        $totalCnt = $buildingSvc->getBuildingCount($where);
        M('Building')->getLastSql();
        $where['special'] = array('neq','');
        $specialCnt = $buildingSvc->getBuildingCount($where);
        $this->assign('totalCnt', $totalCnt);
        $this->assign('specialCnt', $specialCnt);
        $special = I('special');
        if (empty($special)) {
            unset($where['special']);
        }

        //查询
        $field = 'id,title,image,memo,addtime';
        $pagesize = 6;
        $order = I('order');
        if (empty($order))
            $order = "id DESC";
        $count = $buildingSvc->getBuildingCount($where);
        $pager = $pager = new \Think\Page($count, $pagesize);
        $multi = $pager->show();
        $list = $buildingSvc->getBuildings($where, $pager->firstRow.','.$pager->listRows, $order);
        foreach($list as $k=>$v) {
            $list[$k]['url'] = U(ucfirst($this->code).'/detail/'.$v['id']);
            $where2['buildingid'][1] = $v['id'];
            $list[$k]['layouts'] = $buildingSvc->getLayouts($v['id'], 4);
        }
        $this->assign('list', $list);
        $orderArr = array(
            array('title'=>'默认排序', code=>''),
            array('title'=>'售价', 'code' => 'price'),
            array('title'=>'开盘时间','code'=>'lastopentime'),
            array('title'=>'佣金', 'code'=>'brokerage')
        );
        $this->assign('orderList', $orderArr);
        $this->page['channel'] = $this->code;
        $this->page['title'] = $channel['title'] . '-' . $this->page['title'];
        $this->assign('type', $channel);

        $this->assign('multi', $multi);
		//推荐项目排行
		$where = array();
		$order = 'recommendnum DESC';
		$list = $buildingSvc->getBuildings($where, 10, $order);
		$this->assign('recommendList', $list);
        $this->display();
    }

    public function detail() {
        $channel = $this->arctypeSvc->getType($this->code);
        $id = I('get.id');
        $this->checkId($id);
        $model = D('Building', 'Service');
        $model->updateStat($id, 'viewnum');
        $data = $model->getBuilding($id);
        $this->assign('data', $data);
        //相册
        //$albumTypes = $this->cacheSvc->getData('BuildingAlbumTypeMlt');
        $list = $model->getAlbum($data['id']);
        /*foreach($albumTypes as $k=>$v) {
            $thumb = $model->getAlbum($data['id'], $v['id']);
            //if (!empty($thumb)) {
                $v['items'] = $thumb;
                $list[] = $v;
            //}
        }*/
        //unset($albumTypes);
        $this->assign('albumImages', $list);
        //项目动态
        $arcSvc = D('Article','Service');
        $newsList = $arcSvc->getArticlesByType('news/xmdt', 10);
        $this->assign('newsList', $newsList);

        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $this->crumbs[] = array('title'=>$data['title'], 'url'=>'');
        $this->page['channel']= $this->code;
        $this->page['title'] = $data['title'] . '-' . $this->page['title'];

        $this->display();
    }

    public function recommend() {
        if (IS_POST) {
            $model = M('Client');
            $data = $model->create();
            if (empty($data['surname']) || empty($data['phone']))
                $this->error('请填写客户姓名和联系电话');
            $buildings = I('buildings');
            $buildingarr = array();
            foreach($buildings as $k=>$v) {
                if (!empty($v))
                    $buildingarr[] = $v;
            }
            $data['buildings'] = ext_implode(' ', $buildingarr);
            $data['addtime'] = time();
            $data['memid'] = $this->member['id'];
            $model->add($data);
            $this->redirect('Broker/resuccess');
        } else {
            $id = I('id');
            $data = D('Building','Service')->getBuilding($id);
            $this->assign('data', $data);
            $list = $this->cacheSvc->getData('DistrictMlt');
            $this->assign('districtList', $list);
            $list = $this->cacheSvc->getData('HouseLayoutMlt', $list);
            $this->assign('houseLayoutList', $list);
            $list = $this->cacheSvc->getData('LayoutAmountMlt', $list);
            $this->assign('layoutAmountList', $list);
            $this->display();
        }
    }

    public function desktop() {
		$channel = $this->arctypeSvc->getType($this->code);
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $uid = $this->member['id'];
        $stats = D('Client','Service')->getStats($uid);
        $this->assign("stats", $stats);
	
        $this->page['channel'] = $this->code;
        $this->page['title'] = $channel['title'] . '-' . $this->page['title'];
        $this->assign('type', $channel);
        $this->assign('sidenav','desktop');
        $this->display();
    }
    
    public function client() {
        $stats = D('Client', 'Service')->getStats();
        $this->assign('stats', $stats);
        $this->assign('sidenav','client');
        $this->display();
    }
    
    public function profile() {
        $uid = $this->member['id'];
        $data = D('Broker','Service')->getBroker($uid);
        $this->assign("data", $data);
        $this->assign('sidenav','profile');
        $this->display();
    }
    public function addwithdraw() {
        if (IS_POST) {
            $mobile = $this->member['mobile'];
            $checkcode = I('checkcode');
            if (!$this->checkSmscode($checkcode, $mobile)) {
                $this->error('验证码错误');
            }
            $model = M('withdraw');
            $data = $model->create();
            $data['memid'] = $this->member['id'];
            $data['memname'] = $this->member['username'];
            $data['addtime'] = time();
            $model->add($data);
            $this->success('申请提交成功', U('Broker/withdraw'));
        } else {
            $this->assign('sidenav','withdraw');
            $this->display();
        }
    }
    public function withdraw() {
        $uid = $this->member['id'];
        $where = array(
            'memid' => array('eq', $uid)
        );
        $model = M('Withdraw');
        $pagesize = 10;
        $count = $model->where($where)->count();
        $pager = $pager = new \Think\Page($count, $pagesize);
        $multi = $pager->show();
        $order = 'id DESC';
        $list = $model->where($where)->limit($pager->firstRow.','.$pager->listRows)->order($order)->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('sidenav','withdraw');
        $this->display();
    }

    public function daybook() {
        $uid = $this->member['id'];
        $where = array(
            'memid' => array('eq', $uid)
        );
        $model = M('Daybook');
        $pagesize = 10;
        $count = $model->where($where)->count();
        $pager = $pager = new \Think\Page($count, $pagesize);
        $multi = $pager->show();
        $order = 'id DESC';
        $list = $model->where($where)->limit($pager->firstRow.','.$pager->listRows)->order($order)->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('sidenav','withdraw');
        $this->display();
    }
    public function favorite() {
        $uid = $this->member['id'];
        $where = array(
            'memid' => array('eq', $uid),
            'model' => array('eq', 'building')
        );
        $model = M('Favorite');
        $pagesize = 10;
        $count = $model->where($where)->count();
        $pager = $pager = new \Think\Page($count, $pagesize);
        $multi = $pager->show();
        $order = 'id DESC';
        $list = $model->field('b.*')->join('__BUILDING__ b ON b.id = __FAVORITE__.itemid')->where($where)->limit($pager->firstRow.','.$pager->listRows)->order($order)->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('sidenav','favorite');
        $this->display();
    }
	 public function register(){
        $channel = $this->arctypeSvc->getType($this->code);
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $this->crumbs[] = array('title'=>'注册', 'url'=>'');
         $this->page['channel'] = $this->code;
        $this->page['title'] = '经纪人注册' . '-' . $this->page['title'];
        $this->display();
    }
}
