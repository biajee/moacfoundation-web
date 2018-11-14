<?php
namespace Common\Service;
class DealerService {

    protected function formatData(&$data) {
        if ($data['image'])
            $data['image'] = fix_imgurl($data['image']);
    }

    public function getDealers($fields='id,title,image,link,phone') {
        $model = M('Dealer');
        $list = $model->field($fields)->select();
        foreach($list as $k=>$v) {
            $this->formatData($list[$k]);
        }
        return $list;
    }
    public  function getDealer($where, $fields='*') {
        $model = M('Dealer');
        if (is_numeric($where)) {
            $where = array('id'=>array('eq', $where));
        }
        $data = $model->field($fields)->where($where)->find();
        $this->formatData($data);
        return $data;
    }

    public function getDealerById ($id, $fields='*') {
        $where = array(
            'id' => array('eq', $id)
        );
        return $this->getDealer($where);
    }

    public function getDealerByName($name, $fields='*') {
        $where = array(
            'subtitle' => array('eq', $name)
        );
        return $this->getDealer($where);
    }
    public function getRanks() {
        $model = M('AppDealerrank');
        $where = array('status'=>array('gt', 0));
        $order = 'sortno DESC,id ASC';
        $fields = 'id,title';
        $list = $model->field($fields)->where($where)->order($order)->select();
        return $list;
    }
    public function getRank($code) {
        $model = M('AppDealerrank');
        $where = array(
            'code' => array('eq', $code)
        );
        $data = $model->where($where)->find();
        if ($data & !empty($data['image'])) {
            $data['image'] = fix_imgurl($data['image']);
        }
        return $data;
    }

    public  function getRankItems($rankid) {
        static $_items;
        $model = M('AppDealerrankitem');
        $deaModel = $this;
        $parModel = service('Parity');
        $where = array(
            'drankid' => array('eq', $rankid),
            'dealerid' => array('gt', 0)
        );
        $order = "sortno DESC, id ASC";
        $items = $model->where($where)->order($order)->select();
        foreach($items as $k=>$v) {
            $dealerid = $v['dealerid'];
            if ($_items[$dealerid])
                $item = $_items[$dealerid];
            else {
                $item = $deaModel->getDealer($v['dealerid'], 'id,title,image,link,phone');
                $item['paritynum'] = $parModel->getItemsCountByDealerId($v['dealerid']);
                $_items[$dealerid] = $item;
            }
            $list[] = $item;
        }
        return $list;
    }
}