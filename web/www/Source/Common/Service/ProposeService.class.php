<?php
namespace Common\Service;
/**
 * Class ProposeService 推广服务
 * @package Common\Service
 */
class ProposeService {

    public function getPropose($where) {
        $model = M('AppPropose');
        return $model->where($where)->find();
    }

    public function getProposeByCode($code) {
        $where = array(
            'code' => array('eq', $code)
        );
        return $this->getPropose($where);
    }

    public function getProposeItems($proposeid, $count) {
        $where = array('proposeid'=>array('eq', $proposeid));
        $model = M('AppProposeitem');
        $order = 'sortno ASC, id DESC';
        $limit = $count ? $count:5;
        $list = $model->where($where)->order($order)->select();
        $items = array();
        if ($list) {
            $bModel = service('Building');
            $fields = 'id,title,image,price,unit,propertytype,zone';
            foreach($list as $k=> $v) {
                $items[] = $bModel->getBuilding($v['buildingid'], $fields, false);
            }
        } else {
            $items = null;
        }
        return $items;
    }
}