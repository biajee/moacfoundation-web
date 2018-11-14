<?php
namespace Common\Service;
/**
 * Class QuoteRankService 推广服务
 * @package Common\Service
 */
class QuoterankService {

    public function getRanks() {
        $model = M('AppRank');
        $where = array('status'=>array('gt', 0));
        $order = 'sortno DESC,id ASC';
        $fields = 'id,title';
        $list = $model->field($fields)->where($where)->order($order)->select();
        return $list;
    }

    public function getRank($where) {
        $model = M('AppRank');
        return $model->where($where)->find();
    }

    public function getRankByCode($code) {
        $where = array(
            'code' => array('eq', $code)
        );
        return $this->getRank($where);
    }

    public function getRankItems($rankid) {
        $where = array('rankid'=>array('eq', $rankid));
        $model = M('AppRankitem');
        $order = 'sortno DESC, id ASC';
        $list = $model->where($where)->order($order)->select();
        $items = array();
        if ($list) {
            $bModel = service('Building');
            $fields = 'id,title,district,zone,address,image,price,unit,propertytype,mainproduct';
            foreach($list as $k=> $v) {
				$item = $bModel->getBuilding($v['buildingid'], $fields, false);
				if ($item)
					$items[] = $item;
            }
        } else {
            $items = null;
        }
        return $items;
    }
}