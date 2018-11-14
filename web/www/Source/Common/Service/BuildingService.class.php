<?php
// +----------------------------------------------------------------------
// | 天津购房网
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://www.33hl.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: WangXianFeng <365102010@qq.com>
// +----------------------------------------------------------------------
namespace Common\Service;
/**
 * Class BuildingService
 * @package Common\Service
 */
class BuildingService extends BaseService
{
    protected $rankTypes = null;
    protected function getList($name) {
        $cacheSvc = D('Cache', 'Service');
        return $cacheSvc->getData($name);
    }
	
    protected function parseRange($field, $range) {
        $condition = null;
        if (empty($range[1]))
            $condition = array('gt', $range[0]);
        else {
            if ($range[0] == $range[1]) {
                $condition = array('eq', $range[0]);
            } else {
                $condition = array(
                    array('gt', $range[0]),
                    array('elt', $range[1])
                );
            }
        }
        return $condition;
    }
    public function transSubway($subway) {
        $arr = explode(',', $subway);
        $subways = $this->getList('SubwayMap');
        $res = array();
        foreach($arr as $k=>$v) {
            $res[$v] = $subways[$v];
        }
        return $res;
    }
	//格式化数据
    public function formatData(&$data) {
		$price = $data['price'];
		$unit = $data['unit'];
		if ($unit == '元/套') {
            $price = $price/10000;
            $unit = '万元/套';
        }
		//$data['price2'] = $data['price'];
		//$data['unit2'] = $data['unit'];
		$data['price'] = $price;
		$data['unit'] = $unit;
        if (!empty($data['image'])) {
			//$data['image2'] = $data['image'];
            $data['image'] = fix_imgurl($data['image']);
		}
		if ($data['tags']) {
            $data['tagarr'] = explode(' ', trim($data['tags'], ' '));
        }
		
		if (!empty($data['content'])) {
			$data['content'] = fix_html($data['content']);
		}
	}
    /**
     * 预处理数据
     * @param $data
     */
    protected function prepareData(&$data)
    {
        $data['tags'] = str_forsearch($data['tags']);
        //附加数据
        $data['Addon'] = array(
            'facilities' => $data['facilities'],
            //'images' => $data['images'],
            //'album' => $data['album'],
            'content' => $data['content']
        );
        unset($data['facilities']);
        //unset($data['images']);
        //unset($data['album']);
        unset($data['content']);
        if ($data['oldprice'] == '')
            $data['oldprice'] = $data['price'];
        if (empty($data['lastopentime'])) {
            $data['lastopentime'] = 0;
        } else {
            $data['lastopentime'] = strtotime($data['lastopentime']);
        }
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        //$data['updatetime'] = time();
    }

    /**
     * 添加信息
     * @param $data 数据
     * @return int id
     */
    public function addBuilding($data)
    {
        $this->prepareData($data);
        $id = D('Building')->relation('Addon')->add($data);
        //更新附件信息
        $key = 'Building/' . $id;
        $this->bindAttach($key);
        return $id;
    }

    /**
     * @param $data 保存的数据
     * @return int 影响条数
     */
    public function updateBuilding($data)
    {
        if (empty($data['id'])) {
            E('信息不存在');
        }
		$old = $this->getBuilding($data['id']);
		if ($old['price'] != $data['price']) {
            $data['pricetrend'] = $data['price'] - $old['price'];
            $data['updatetime'] = time();
            $needNotice = true;
        }
		$carr = array('price','preevent','discount','salestatus');
		$changed = false;
		foreach($carr as $v) {
			if ($old[$v] != $data[$v]) {
				$changed = true;
				break;
			}
		}
		if ($changed)
			$data['quotetime'] = time();
        $this->prepareData($data);
        $result = D('Building')->relation('Addon')->save($data);
        $key = 'Building/' . $data['id'];
        $this->bindAttach($key);
        //通知变化
        if ($needNotice) {
            M('AppParity')->save(array('id'=>$data['id'],'updatetime'=>time()));
        }
        return $result;
    }

    /**
     * 获取数量
     * @param $where
     * @return mixed
     */
    public function getBuildingCount($where) {
        return M('Building')->where($where)->count();
    }
    
    public function getBuildings($where, $limit = '', $order = 'id DESC', $fields='*')
    {
        $model = M('Building');
        $model->field($fields)
            ->where($where)
            ->order($order);
		
        if (!empty($limit))
            $model->limit($limit);
        $list = $model->select();
        foreach($list as $k => $v) {
            $this->formatData($list[$k]);  
        }
        return $list;
    }

    public function getBuildingsByRank($code, $limit = '10')
    {
        $type = $this->getRankType($code);
        if (empty($type)) {
            return array();
        }
        $field = $type['key'];
        $where[$field] = array('gt', 0);
        $order = "$field DESC";
        return $this->getBuildings($where, $limit, $order);
    }

    public function getBuilding($id, $fields='*', $relation=true)
    {
        $model = D('Building');
        if ($relation)
            $model->relation('Addon');
        $data = $model->field($fields)->find($id);
        if (!empty($subway)) {
            $subway = $this->transSubway($data['subway']);
            $data['subwaystr'] = implode(' ', $subway);
        }
        if (!empty($data['tagarr']))
            $data['tagarr'] = explode(' ', trim($data['tags'],' '));
        if ($relation) {
            $layouts = $this->getLayouts($id);
            $data['layouts'] = $layouts;
        }

        $this->formatData($data);
        return $data;
    }

    public function updateStat($id, $item)
    {
        $model = M('Building');
        $data = array('id' => $id,
            $item => array('exp', "{$item}+1")
        );
        $model->save($data);
        $num = $model->where(array('id' => array('eq', $id)))->getField($item);
        return $num;
    }

    public function getRankTypes()
    {
        if (empty($this->_rankTypes)) {
            $cacheSvc = D('Cache', 'Service');
            $this->_rankTypes = $cacheSvc->getData('BuildingRankMlt');
        }
        return $this->_rankTypes;
    }

    public function getRankType($code)
    {
        $this->getRankTypes();
        foreach ($this->_rankTypes as $k => $v) {
            if ($v['code'] == $code) {
                return $v;
            }

        }
    }

    public function delBuilding($id)
    {
        D('Building')->deleteOne($id);
        $key = 'Building/' . $id;
        $this->delAttach($key);
    }

    public function getLayouts($where, $limit=10, $order='sortno ASC, id ASC', $fields='')
    {
        $model = M('Buildinglayout');
        if (!is_array($where))
            $where = array(
                'buildingid' => array('eq', $where)
            );
        if (empty($limit))
            $limit = 50;
        if (empty($fields))
            $fields = 'bedroom,hall,kitchen,bathroom,grossarea,price,amount';
        $list = $model->field($fields)->where($where)->order($order)->limit($limit)->select();
        return $list;
    }
    //获取相册
    public function getAlbum($id, $catid=0)
    {
        $model = M('Buildingalbum');
        $where = array(
            'buildingid' => array('eq',$id),
        );
        if (!empty($catid)) {
           $where['catid'] = array('eq', $catid);
        }
        $fields = 'title,image';
        $order = "sortno DESC, id asc";
        $list = $model->field($fields)->where($where)->order($order)->select();
        foreach($list as $k => $v) {
            $list[$k]['image'] = fix_imgurl($v['image']);
        }
        return $list;
    }
    //获取比价信息
    public function getParity($id, $limit=false, $order='sortno DESC, id ASC')
    {
        $model = M('Buildingparity');
        $where = array(
            'buildingid' => array('eq',$id),
        );
        $fields = '*';
		if (empty($order))
			$order = "sortno DESC, id ASC";
        $model->where($where)->order($order);
		if ($limit) {
			$model->limit($limit);
		}
		$list = $model->select();
		$baseurl = U('/',null, true, true).'assets/img/parity/';
		foreach($list as $k=>$v) {
			$list[$k]['image'] = $baseurl.urlencode($v['title']).'.png';
		}
        return $list;
    }
	
	public function getParityCount($id)
    {
        $model = M('Buildingparity');
        $where = array(
            'buildingid' => array('eq',$id),
        );
        $count = $model->where($where)->count();
        return $count;
    }
	
    public function getUlike($limit) {
        //猜你喜欢
        $code = 'gxqlp';
        $type = $this->getRankType($code);
        $list = null;
        if (!empty($type)) {
            $field = $type['key'];
            $where[$field] = array('gt', 0);
            $order = "$field DESC";
            $cnt = $this->getBuildingCount($where);
            if (empty($limit))
                $limit = 4;
            $start = 0;
            if ($cnt > $limit) {
                $last = $cnt-$limit;
                $start = mt_rand(0, $last);
            }
            $fields = 'id,title,image,district,tagline,tags,price,unit,coupon';
            $list = $this->getBuildings($where, "$start,$limit", $order, $fields);
        }
        return $list;
    }
	
	public function getSimilar($id, $limit, $dv=3000) {
		$info = $this->getBuilding($id,'id,price,propertytype');
		if (empty($info)) {
			E('参数错误');
		}
		$where = array();
		$where['id'] = array('neq', $id);
		
		if ($info['district']) {
			$where['district'] = array('eq', $info['district']);
		}
		
		if ($info['price']>0) {
			$prMin = $info['price'] - $dv;
			$prMax = $info['price'] + $dv;
			$where['price'] = array(array('gt', $prMin),array('lt', $prMax));
		} else {
			$where['price'] = array('eq',0);
		}
		
		
		/*if (!empty($info['propertytype'])) {
			$pt = trim($info['propertytype']);
			$ptArr = explode(' ', $pt);
			if (count($ptArr)>1) {
				$where['propertytype'] = array('like', "%{$pt}%");
			} else {
				$where2 = array();
				foreach($ptArr as $pv) {
					$where2[] = array('like', "%{$pv}%");
				}
				$where2[] = 'or';
				$where['propertytype'] = $where2;
			}
			
		}*/
		$order = 'updatetime DESC';
		$fields = 'id,title,image,district,address,tagline,tags,price,unit,coupon';
		$list = $this->getBuildings($where, $limit, $order, $fields);
		return $list;
	}
    //测评相关
    public function wantReview($id) {
        $data = array(
            'id' => $id,
            'wantreviewnum' => array('exp','wantreviewnum + 1')
        );
        $this->updateBuilding($data);
    }

    
}