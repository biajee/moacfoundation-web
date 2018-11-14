<?php
namespace Common\Service;
/**
 * Class ReviewService 评论服务
 * @package Common\Service
 */
class ReviewService {

    public function addReview($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Review')->add($data);
    }

    public function getReviews($where, $limit=10, $order="id DESC") {
        $list = M('Review')->where($where)->order($order)->limit($limit)->select();
        foreach($list as &$v) {
            $this->formatData($v);
        }
        return  $list;
    }
	
	public function delReview($where) {
		return M('Review')->where($where)->delete();
	}
	
	public function getReview($where) {
		return M('Review')->where($where)->find();
	}
	
	public function getReviewCount($where) {
		return M('Review')->where($where)->count();
	}

	public function formatData(&$data) {
        $modelMember = service('Member');
        $data['member'] = $modelMember->getMemberById($data['memid']);
        $data['other'] = $modelMember->getMemberById($data['otherid']);
    }
}