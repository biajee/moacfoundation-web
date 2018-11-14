<?php
namespace Common\Service;
/**
 * Class CommentService 评论服务
 * @package Common\Service
 */
class CommentService {

    public function addComment($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Comment')->add($data);
    }
    public function getComment($id) {
        return M('Comment')->find($id);
    }
    public function getComments($where, $limit=10, $order="id DESC") {
        $modelMember = service('Member');
        $users = array();
        $list = M('Comment')->where($where)->order($order)->limit($limit)->select();
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

    public function getCommentCount($where) {
        return M('Comment')->where($where)->count();
    }

    public function updatePraise($id) {
        $data = array(
            'id' => $id,
            'praisenum' => array('exp','prisenum+1')
        );
        M('Comment')->save($data);
    }

    public function getStat($module, $itemid) {
        $model = M('Comment');
        $where = array(
            'module' => $module,
            'itemid' => $itemid
        );
        return $model->where($where)->count();
    }

}