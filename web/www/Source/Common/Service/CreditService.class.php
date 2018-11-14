<?php
namespace Common\Service;
/**
 * Class CreditService 积分服务
 * @package Common\Service
 */
class CreditService {

    public function addCredit($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Credit')->add($data);
    }

    public function updateCredit($data) {
        $data['updatetime'] = time();
        return M('Credit')->save($data);
    }

    public function getCredits($where, $limit=10, $order="sortno DESC") {
        $list = M('Credit')->where($where)->order($order)->limit($limit)->select();
        foreach($list as $k=>$v) {
            $list[$k]['image'] = fix_imgurl($v['image']);
        }
        return  $list;
    }

    public function getCredit($key) {
        if (is_numeric($key)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = array($mk=>array('eq', $key));
		$data = M('Credit')->where($where)->find();
		$data['image'] = fix_imgurl($data['image']);
        return $data;
    }

    public function delCredit($id) {
        M('Credit')->delete($id);
    }

    public function delCredits($where) {
        M('Credit')->where($where)->delete();
    }

}