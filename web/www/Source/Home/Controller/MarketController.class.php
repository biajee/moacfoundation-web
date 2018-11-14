<?php
namespace Home\Controller;

class MarketController extends BaseController {
    private $code = 'market';

    public function index() {
        $channel = $this->arctypeSvc->getType($this->code);
        $this->crumbs[] = array('title'=>$channel['title'], 'url'=>$channel['url']);
        $buildingSvc = D('Resold','Service');
        $filters = array(
            'district'=>'District',
            'amount' => 'ResoldAmount',
            'area' => 'ResoldArea',
            'layout' => 'HouseLayout',
            'direction' => 'Direction',
            'age' => 'ResoldAge',
            'floor' => 'ResoldFloor',
            'tag' => 'ResoldTag'
        );
        $filterList = array();
        foreach( $filters as $key => $v) {
            $k = $v.'Mlt';
            $filterList[$v] = $this->cacheSvc->getData($k);
            $this->assign(lcfirst($v).'List', $filterList[$v]);
        }
        $special = array(
            '满五年唯一','近地铁','学区房','不限购'
        );
        $this->assign('special', $special);
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
            $items = & $filterList['District'];
            $item = $items[$val];
            $where['district'] = array('eq', $item['title']);
            $selector[$code] = $item;
        }
        //总价区间
        $code = 'amount';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList['ResoldAmount'];
            $item = $items[$val];
            $range = $item['param'];
            $where['price'] = parse_range($range);
            $selector[$code] = $item;
        }
        //面积区间
        $code = 'area';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList['ResoldArea'];
            $item = $items[$val];
            $range = $item['param'];
            $where['grossarea'] =  parse_range($range);
            $selector[$code] = $item;
        }
        //户型
        $code = 'layout';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList['HouseLayout'];
            $item = $items[$val];
            $range = $item['param'];
            $where['bedroom'] =  parse_range($range);
            $selector[$code] = $item;
        }
        //朝向
        $code = 'direction';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList['Direction'];
            $item = $items[$val];
            $where['buildingtype'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }
        //楼龄
        $code = 'age';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList['ResoldAge'];
            $item = $items[$val];
            $range = $item['param'];
            $year = data('Y', time());
            $where[$year.'-builttime'] = parse_range($range);
            $selector[$code] = $item;
        }
        //
        $code = '楼层';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList['ResoldFloor'];
            $item = $items[$val];
            $range = $item['param'];
            $where['floor'] = parse_range($range);
            $selector[$code] = $item;
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
        //特色
        $code = 'feature';
        $val = I($code);
        if ($val != '') {
            $items = & $filterList[$code]['items'];
            $item = $items[$val];
            $where['feature'] = array('like', '% ' . $item['title'] . ' %');
            $selector[$code] = $item;
        }

        $this->assign('selectorList', $selector);
        //统计
        $totalCnt = $buildingSvc->getResoldCount($where);
         M('Resold')->getLastSql();
        $where['special'] = array('neq','');
        $specialCnt = $buildingSvc->getResoldCount($where);
        $this->assign('totalCnt', $totalCnt);
        $this->assign('specialCnt', $specialCnt);
        $special = I('special');
        if (empty($special)) {
            unset($where['special']);
        }
        
        //查询
        $field = '*';
        $pagesize = 12;
        $order = "id DESC";
        $count = $buildingSvc->getResoldCount($where);
        $pager = $pager = new \Think\Page($count, $pagesize);
        $multi = $pager->show();
        $list = $buildingSvc->getResolds($where, $pager->firstRow.','.$pager->listRows, $order);
        foreach($list as $k=>$v) {
            $list[$k]['url'] = U(ucfirst($this->code).'/detail/'.$v['id']);
            $where2['buildingid'][1] = $v['id'];
        }
        $this->assign('list', $list);
        //排序
        $orderArr = array(
            array('title'=>'默认', code=>''),
            array('title'=>'最新', 'code' => 'id'),
            array('title'=>'总价','code'=>'amount'),
            array('title'=>'单价','code'=>'price'),
        );
        $this->assign('orderList', $orderArr);
        $this->page['channel'] = $this->code;
        $this->page['title'] = $channel['title'] . '-' . $this->page['title'];
        $this->assign('type', $channel);

        $this->assign('multi', $multi);
        $this->display();
    }

    public function detail() {
        $channel = $this->arctypeSvc->getType($this->code);
        $id = I('get.id');
        $this->checkId($id);
        $model = D('Resold', 'Service');
        $data = $model->getResold($id);
        $data['tagarr'] = explode(' ', trim($data['feature'],' '));
        $data['addtime'] = date('Y-m-d',$data['addtime']);
        $this->assign('data', $data);
        //相册
        //$albumTypes = $this->cacheSvc->getData('ResoldAlbumTypeMlt');
       // $list = $model->getAlbum($data['id']);
        /*foreach($albumTypes as $k=>$v) {
            $thumb = $model->getAlbum($data['id'], $v['id']);
            //if (!empty($thumb)) {
                $v['items'] = $thumb;
                $list[] = $v;
            //}
        }*/
        //unset($albumTypes);
        $list = explode(',', $data['images']);
        $this->assign('albumImages', $list);
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
        $strwhere ="street = '{$street}'";
        $tqyList = $model->getResolds($where,12);
        $this->assign('sameReslidList',$tqyList);
        //同价位新房
         $buildingSvc = D('Building','Service');
         $budprice = $data["price"];
         $preprice = $budprice -1000;
         $nexprice = $budprice + 1000;
         $budwhere = "price >= {$preprice} and price <= {$nexprice}";
         $tjyList = $buildingSvc->getBuildings($budwhere,3);
         $this->assign("samePriceList",$tjyList);
        $this->display();
    }

    public function publish() {
		 $this->page['channel'] = "resold";
        if (! IS_POST) {
            $listArr = array ('District', 'Streets', 'HouseType', 'Decoration', 'Direction', 'ResoldAge', 'PropertyRight', 'RightPeriod');
            foreach($listArr as $v) {
                $list = $this->cacheSvc->getData($v.'Mlt');
                $this->assign(lcfirst($v)."List", $list);
            }
            $this->display();
        } else {
            $model = D('Resold');
            if ($data = $model->create()) {
                if (empty($this->member)) { //未登录
                    $mobile = $data['phone'];
                    try {
                        $this->mobileLogin($mobile);
                    } catch(\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
                $data['feature'] = ext_implode(' ', $_GET['feature']);
                $data['memid'] = $this->member['id'];
                $data['memname'] = $this->member['username'];
                $data['addtime'] = time();
                $model->add($data);
                $this->success('发布成功', U('Resold/index'));
            } else {
                $this->error($model->getError());
            }

        }
    }

}