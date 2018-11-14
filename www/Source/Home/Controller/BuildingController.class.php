<?php
namespace Home\Controller;

class BuildingController extends BaseController {
    private $code = 'building';

    public function index() {
        $channel = $this->arctypeSvc->getType($this->code);
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $buildingSvc = D('Building','Service');
        $filters = array(
            array(
                'district' => array('code'=>'District','title'=>'区域'),
                'subway' => array('code'=>'Subway','title'=>'地铁'),
                'price' => array('code'=>'BuildingPrice','title'=>'房价'),
                'area' => array('code'=>'BuildingArea','title'=>'面积'),
                'htype' => array('code'=>'HouseType','title'=>'类型'),
                'feature' => array('code'=>'BuildingFeature','title'=>'楼盘特色'),
               /* 'tag' => array('code'=>'BuildingTag','title'=>'楼盘标签')*/
            ),
            array(
                'layout' => array('code'=>'HouseLayout','title'=>'户型'),
                'district' => array('code'=>'District','title'=>'区域'),
                'subway' => array('code'=>'Subway',title=>'地铁'),
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
		 $featureList = $this->cacheSvc->getData("BuildingFeatureMlt");
       $this->assign("featureList",$featureList);
        //查询条件
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
        //物业类型
        $code = 'htype';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['propertytype'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //地铁
        $code = 'subway';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['subway'] = array('like', '%,' . $item['key'] . ',%');
            $selector[$code] = $item;
        }
        //特色
        $code = 'feature';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['feature'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //标签
       /* $code = 'tag';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['tags'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }*/
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
            $order = "updatetime DESC";
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
        //排序
        $orderArr = array(
            array('title'=>'默认排序', code=>''),
            array('title'=>'售价', 'code' => 'price'),
            array('title'=>'开盘时间','code'=>'lastopentime'),
        );
        $this->assign('orderList', $orderArr);
        //优惠排行
        $code = 'yhph';
        $list = $buildingSvc->getBuildingsByRank($code, 8);
        $this->assign('yhphList', $list);
        //最新评测
        $where = array(
            'isreview'=>array('gt',0)
        );
        $order = 'isreview DESC';
        $list = $buildingSvc->getBuildings($where, 6, $order);
        $this->assign('zxpcList', $list);
        //看房团
        $tourSvc = D('Tour', 'Service');
        $tour = $tourSvc->getTour(5);
        foreach($tour['items'] as $k=>$v) {
            $tour['items'][$k]['url'] = U('Building/detail/'.$v['id']);
        }
        $this->assign('tour', $tour);
        $this->page['channel'] = $this->code;
        $this->page['title'] = $channel['title'] . '-' . $this->page['title'];
        $this->assign('type', $channel);

        $this->assign('multi', $multi);
		
		
		/*$lx=I('type');
		$val=I('value');
		if($value!= '')
		{
			$this->assign('lx',$lx);
			$this->assign('val',$val);
		}*/
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
        foreach($list as $k=>$v) {
            $list[$k]['image'] = img2water($v['image']);
        }
        $this->assign('albumImages', $list);
        //项目动态
        $arcSvc = D('Article','Service');
        $type = $this->arctypeSvc->getType('news/xmdt');
        $where = array(
            //'catid' => array('eq', $type['id']),
            'assbuilding' => array('eq', $data['title'])
        );
        $newsList = $arcSvc->getArticles($where, 3);
        $this->assign('newsList', $newsList);
        
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $this->crumbs[] = array('title'=>$data['title'], 'url'=>'');
        $this->page['channel']= $this->code;
        $this->page['title'] = $data['title'] . '-' . $this->page['title'];

		$flag = I('flag');
		$this->assign('flag',$flag);
        $this->display();
    }
    //地图选房
    public function map() {
        $this->display();
    }
 public function tour() {
        //看房团
        $tourSvc = D('Tour', 'Service');
        $data = $tourSvc->getTour(5);
        foreach($data['items'] as $k=>$v) {
            $data['items'][$k]['url'] = U('Building/detail/'.$v['id']);
        }
     
        $this->assign('data', $data);
        //参加列表
        $list = $tourSvc->getApplies($data['id'], 10);
        foreach($list as $k => $v) {
            if (strlen($v['phone'])>7)
                $list[$k]['phonestr'] = substr($v['phone'], 0, 3) . str_repeat('*', strlen($v['phone'])-7) . substr($v['phone'], -4);
        }
        $this->assign('applyList', $list);
        $this->page['channel'] = $this->code;
        $this->crumbs[] = array('title'=>'新房', 'url'=>U('Building/index'));
        $this->crumbs[] = array('title'=>'马上看房', 'url'=>'');
        $this->display();
    }

    public function applyTour() {
        $res = array('error'=>0, 'message'=>'您已报名成功，稍候本站工作人员会和您联系，请您持续关注天津购房网，感谢您的支持！');
        $key = 'applytour'.I('buildingid');
        $cansave = session($key);
        if (!empty($cansave)) { //防止重复提交
            $res['error'] = 1;
            $res['message'] = '您已提交过申请，请勿重复提交';
        } else {
            //字段
            $fields = array('tourid', 'tourname', 'buildingid', 'buildingname', 'surname', 'phone', 'person');
            $data = array();
            foreach($fields as $k => $v)
                $data[$v] = trim(str_fixcn(I($v)));
            if (empty($data['surname']) || empty($data['phone'])) {
                $res['error'] = 1;
                $res['message'] = '请填写您的用户名和联系电话';
            } else {
                $model = D('Tour', 'Service');
                $model->applyTour($data);
                session($key, 1);
            }
        }
        $this->ajaxReturn($res);
    }

    public function call() {
        $key = 'freecall'.I('buildingid');
        $cansave = session($key);
        $res = array('error'=>0, 'message'=>'报名成功！我们会尽快与您联系。');
        if (!empty($cansave)) { //防止重复提交
            $res['error'] = 1;
            $res['message'] = '您已提交过申请，请勿重复提交';
        } else {
            $fields = array('buildingid', 'buildingname', 'surname', 'phone');
            $data = array();
            foreach($fields as $k => $v)
                $data[$v] = trim(str_fixcn(I($v)));
            if (empty($data['surname']) || empty($data['phone'])) {
                $res['error'] = 1;
                $res['message'] = '请填写您的用户名和联系电话';
            } else {
                $model = M('Freecall');
                $data = $model->create();
                $data['addtime'] = time();
                $model->add($data);
                session($key, 1);
            }

        }
        $this->ajaxReturn($res);
    }

    public function wantTour() {
        $res = array('error'=>0, 'message'=>'您的建议已被采纳，我们将尽快开通相关看房路线，敬请等待我们的工作人员和您联系，请您持续关注天津购房网，感谢您的支持！');
        $key = 'wanttour'.I('buildingid');
        $cansave = session($key);
        if (!empty($cansave)) { //防止重复提交
            $res['error'] = 1;
            $res['message'] = '您已提交过申请，请勿重复提交';
        } else {
            $fields = array('buildingid', 'buildingname', 'surname', 'phone');
            $data = array();
            foreach($fields as $k => $v)
                $data[$v] = trim(str_fixcn(I($v)));
            if (empty($data['surname']) || empty($data['phone'])) {
                $res['error'] = 1;
                $res['message'] = '请填写您的用户名和联系电话';
            } else {
                $model = D('Tour', 'Service');
                $model->wantTour($data);
                session($key, 1);
            }
        }
        $this->ajaxReturn($res);
    }

    public function wantReview() {
        $res = array('error'=>0, message=>'您的建议已被采纳，我们将尽快安排团队对该项目进行测评，请您持续关注天津购房网，感谢您的支持！');
        $id = I('id');
        $key = 'wantreview'. $id;
        $cansave = session($key);
        if (!empty($cansave)) { //防止重复提交
            $res['error'] = 1;
            $res['message'] = '您已提交过申请，请勿重复提交';
        } else {
            $model = D('Building','Service');
            $model->wantReview($id);
            session($key, 1);
        }
        $this->ajaxReturn($res);
    }

    public function auto() {
        $keyword = I('keyword');
        $list = null;
        if (!empty($keyword)) {
            $where = array(
                'title' => array('like', "%{$keyword}%")
            );
            $list = D('Building','Service')->getBuildings($where, 50, 'updatetime DESC');
        }
        $this->assign('list', $list);
        $this->display();
    }

    public function news() {
        $page = I('page');
        if (empty($page))
            $page = 1;
        $building = I('bname');
        //项目动态
        $arcSvc = D('Article','Service');
        //$type = $this->arctypeSvc->getType('news/xmdt');
        $where = array(
            //'catid' => array('eq', $type['id']),
            'assbuilding' => array('eq', $building)
        );
        $pagesize = 3;
        $start = ($page-1)*$pagesize;
        $limit = "$start,$pagesize";
        $newsList = $arcSvc->getArticles($where, $limit);
        if ($newsList) {
            $this->assign('newsList', $newsList);
            $this->display();
        } else {
            echo '';
        }
    }
}