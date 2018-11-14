<?php
namespace Common\Service;
/**
 * Class QuotePushService 推广服务
 * @package Common\Service
 */
class QuotepushService {

    public function getPush($where) {
        $model = M('AppPush');
        return $model->where($where)->find();
    }

    public function getPushByCode($code) {
        $where = array(
            'code' => array('eq', $code)
        );
        return $this->getPush($where);
    }

    public function getPushItems($where) {
        $model = M('AppPushitem');
        $order = 'sortno DESC, id ASC';
        //$limit = $count ? $count:5;
        $list = $model->where($where)->order($order)->select();
        $items = array();
        if ($list) {
            $bModel = service('Building');
            $fields = 'id,title,district,zone,address,image,price,unit,propertytype';
            foreach($list as $k=> $v) {
                $item = $bModel->getBuilding($v['buildingid'], $fields, false);
                $item['reason'] = $v['reason'];
                $item['district'] = $v['district'];
                $item['zone'] = $v['zone'];
                $items[] = $item;
            }
        } else {
            $items = null;
        }
        return $items;
    }
}