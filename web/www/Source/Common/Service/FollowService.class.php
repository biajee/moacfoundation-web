<?php
namespace Common\Service;
/**
 * Class FollowService 评论服务
 * @package Common\Service
 */
class FollowService {

    public function addFollow($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Follow')->add($data);
    }

    public function getFollows($where, $limit=10, $order="id DESC") {
        $list = M('Follow')->where($where)->order($order)->limit($limit)->select();
        foreach($list as &$v) {
            $this->formatData($v);
        }
        return  $list;
    }
	
	public function delFollow($where) {
		return M('Follow')->where($where)->delete();
	}
	
	public function getFollow($where) {
		return M('Follow')->where($where)->find();
	}
	
	public function getFollowCount($where) {
		return M('Follow')->where($where)->count();
	}

	public function formatData(&$data) {
        $modelMember = service('Member');
        $data['member'] = $modelMember->getMemberById($data['memid']);
        $data['other'] = $modelMember->getMemberById($data['otherid']);
    }
}