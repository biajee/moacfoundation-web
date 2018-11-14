<?php
namespace Common\Service;
/**
 * Class DaybookService 积分服务
 * @package Common\Service
 */
class DaybookService {

    public function addDaybook($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Daybook')->add($data);
    }

    public function updateDaybook($data) {
        $data['updatetime'] = time();
        return M('Daybook')->save($data);
    }

    public function getDaybooks($where, $limit=10, $order="sortno DESC") {
        $list = M('Daybook')->where($where)->order($order)->limit($limit)->select();
        foreach($list as $k=>$v) {
            $list[$k]['image'] = fix_imgurl($v['image']);
        }
        return  $list;
    }

    public function getDaybook($key) {
        if (is_numeric($key)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = array($mk=>array('eq', $key));
		$data = M('Daybook')->where($where)->find();
		$data['image'] = fix_imgurl($data['image']);
        return $data;
    }

    public function delDaybook($id) {
        M('Daybook')->delete($id);
    }

    public function delDaybooks($where) {
        M('Daybook')->where($where)->delete();
    }


}