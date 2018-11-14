<?php
namespace Home\Controller;

class ResoldController extends BaseController {
    private $code = 'resold';
    public function index2() {
        $channel = $this->arctypeSvc->getType($this->code);
        $this->page['channel'] = $this->code;
        $this->page['title'] = $channel['title'] . '-' . $this->page['title'];
        $this->display();
    }
    public function index() {
        $channel = $this->arctypeSvc->getType($this->code);
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $resoldSvc = D('Resold','Service');
        $filters = array(
            array(
                'district' => array('code'=>'District','title'=>'区域'),
                'amount' => array('code'=>'LayoutAmount','title'=>'售价'),
                'area' => array('code'=>'ResoldArea','title'=>'面积'),
                'layout'=> array('code'=>'HouseLayout','title'=>'房型')
            ),
            array(
                'subway' => array('code'=>'Subway','title'=>'地铁'),
                'amount' => array('code'=>'LayoutAmount','title'=>'售价'),
                'area' => array('code'=>'ResoldArea','title'=>'面积'),
                'htype' => array('code'=>'HouseType','title'=>'房型'),
                //'distance' => array('code'=>'Distance','title'=>'距离')
            ),
            array(
                'district' => array('code'=>'District','title'=>'区域'),
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
        $dropList = array(
            'direction' => array('code'=>'Direction', title=>'朝向'),
            'age' => array('code'=>'ResoldAge', title=>'楼龄'),
            'floor' => array('code'=>'ResoldFloor', title=>'楼层'),
            //'tag' => array('code'=>'ResoldTag', title=>'标签'),
            //'status' => array('code'=>'', title=>'房屋状态'),
        );
        foreach( $dropList as $key => $v) {
            $k = $v['code'].'Mlt';
            $dropList[$key]['key'] = $key;
            $dropList[$key]['items'] = $this->cacheSvc->getData($k);
        }
        $this->assign('dropList', $dropList);
        $checkList = array('满五年唯一/满两年','近地铁','学区房','不限购');
        $this->assign('checkList', $checkList);
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
        $code = 'amount';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where['amount'] = parse_range($range);
            $selector[$code] = $item;
        }
        //单价
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
            $where['grossarea'] = parse_range($range);
            $selector[$code] = $item;
        }
        
        //户型
        $code = 'layout';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where['bedroom'] = parse_range($range);
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
        //距离
        $code = 'distance';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where['distance'] = parse_range($range);
            $selector[$code] = $item;
        }
        //特色
        $code = 'feature';
        $val = I($code);
        if ($val != '') {
            $arr = explode(',', $val);
            $f = array();
            if (count($arr)>1) {
                $where['feature'] = array();
                foreach($arr as $v) {
                    $where['feature'][] = array('like', '% ' . $v . ' %');
                    $f[] = array('key'=>$v,'title'=>$v);
                }
                $where['feature'][] = 'or';
            } else {
                $where['feature'] = array('like', '% ' . $val . ' %');
                $f[] = array('key'=>$val, 'title'=>$val);
            }
            
            $selector['feature'] = $f;
        }
        //朝向
        $code = 'direction';
        $val = I($code);
        if ($val != '') {
            $items = & $dropList[$code]['items'];
            $item = $items[$val];
            $where['direction'] = array('eq', $item['title']);
            $selector[$code] = $item;
        }
        //楼龄
        $code = 'age';
        $val = I($code);
        if ($val != '') {
            $items = & $dropList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $year = date('Y', time());
            $where['builtage'] = parse_range($range);
            $selector[$code] = $item;
        }
        //楼层
        $code = 'floor';
        $val = I($code);
        if ($val != '') {
            $items = & $dropList[$code]['items'];
            $item = $items[$val];
            $range = $item['param'];
            $where['floor'] = parse_range($range);
            $selector[$code] = $item;
        }
        //标签
        $code = 'tag';
        $val = I($code);
        if ($val != '') {
            $items = & $dropList[$code]['items'];
            $item = $items[$val];
            $where['tags'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //房屋状态
        $code = 'status';
        $val = I($code);
        if ($val != '') {
            $items = & $dropList[$code]['items'];
            $item = $items[$val];
            $where['housestatus'] = array('eq', $item['title']);
            $selector[$code] = $item;
        }
		
		$code = 'role';
		$val = I($code);
		if ($val != '') {
			$where['memrole'] = array('eq', $val);
		}
        //统计
        $totalCnt = $resoldSvc->getResoldCount($where);
      
        $where['special'] = array('neq','');
        $specialCnt = $resoldSvc->getResoldCount($where);
        $this->assign('totalCnt', $totalCnt);
        $this->assign('specialCnt', $specialCnt);
        $special = I('special');
        if (empty($special)) {
            unset($where['special']);
        }
        
        $this->assign('selectorList', $selector);
        //查询
        $field = 'id,title,image,memo,addtime';
        $pagesize = 8;
        $order = "id DESC";
        $count = $resoldSvc->getResoldCount($where);
        $pager = $pager = new \Think\Page($count, $pagesize);
        $multi = $pager->show();
        $list = $resoldSvc->getResolds($where, $pager->firstRow.','.$pager->listRows, $order);
        foreach($list as $k=>$v) {
            $list[$k]['url'] = U(ucfirst($this->code).'/detail/'.$v['id']);
            $where2['buildingid'][1] = $v['id'];
        }
        $this->assign('list', $list);
        $this->page['channel'] = $this->code;
        $this->page['title'] = $channel['title'] . '-' . $this->page['title'];
        $this->assign('type', $channel);

        $this->assign('multi', $multi);
        //热门房源
        $where = array(
            'ishot' => array('gt', 0)
        );
        $order = 'ishot DESC';
        $list = $resoldSvc->getResolds($where, 13, $order);
        $this->assign('hotResoldList', $list);
        //二手房资讯
        $code = 'news/esfzx';
        $list = D('Article','Service')->getArticlesByType($code, 11);
        $this->assign('esfzxList', $list);
        //新房
        $where = array(
            'ishot' => array('gt', 0)
        );
        $order = 'ishot DESC';
        $list = D('Building','Service')->getBuildings($where, 4, $order);
        $this->assign('samePriceList', $list);
        
        $this->display();
    }

    public function detail() {
        $channel = $this->arctypeSvc->getType($this->code);
        $id = I('get.id');
        $this->checkId($id);
		
        $model = D('Resold', 'Service');
        $data = $model->getResold($id);
        $data['tagarr'] = explode(' ', trim($data['feature'],' '));
        $this->assign('data', $data);
		
        if (!empty($data['images'])) {
            $list = explode(',', trim($data['images']));
            $this->assign('albumImages', $list);
        }
        //项目动态
        $arcSvc = D('Article','Service');
        $newsList = $arcSvc->getArticlesByType('xmdt', 10);
        $this->assign('newsList', $newsList);
        //
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $this->crumbs[] = array('title'=>$data['title'], 'url'=>'');
        $this->page['channel']= $this->code;
        $this->page['title'] = $data['title'] . '-' . $this->page['title'];
        //同区域二手房
         $street = $data['street'];
        $strwhere ="street='{$street}'";
        $tqyList = $model->getResolds($where,12);
        $this->assign('sameReslidList',$tqyList);
        //同价位新房
         $resoldSvc = D('Building','Service');
         $budprice = $data["price"];
         $preprice = $budprice -1000;
         $nexprice = $budprice + 1000;
         $budwhere = "price >= {$preprice} and price <= {$nexprice}";
         $tjyList = $resoldSvc->getBuildings($budwhere,3);
         $this->assign("samePriceList",$tjyList);
        $this->display();
    }
    //地图选房
    public function map() {
        $this->display();
    }
	
	public function auto() {
        $keyword = I('keyword');
        $list = null;
        if (!empty($keyword)) {
            $where = array(
                'title' => array('like', "%{$keyword}%")
            );
            $list = D('Resold','Service')->getResolds($where, 50, 'addtime DESC');
        }
        $this->assign('list', $list);
        $this->display();
    }
	
}