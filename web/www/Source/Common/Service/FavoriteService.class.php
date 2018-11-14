<?php
namespace Common\Service;
/**
 * Class FavoriteService 评论服务
 * @package Common\Service
 */
class FavoriteService {

    public function addFavorite($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Favorite')->add($data);
    }

    public function getFavorites($where, $limit=10, $order="id DESC") {
        $modelMember = service('Member');
        $users = array();
        $list = M('Favorite')->where($where)->order($order)->limit($limit)->select();
        foreach($list as $k=>$v) {
            $memid = $v['memid'];
            if (!array_key_exists($memid, $users)) {
                $avatar = $modelMember->getAvatar($memid); 
            } else {
                $avatar = $users[$memid];
            }
            $list[$k]['avatar'] = $avatar;
        }
        return  $list;
    }
	
	public function delFavorite($where) {
		return M('Favorite')->where($where)->delete();
	}
	
	public function getFavorite($where) {
		return M('Favorite')->where($where)->find();
	}
	
	public function getFavoriteCount($where) {
		return M('Favorite')->where($where)->count();
	}
}