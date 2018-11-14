<?php
namespace Common\Service;
/**
 * Class FriendlinkService 友情链接
 * @package Common\Service
 */
class FriendlinkService extends BaseService {
    protected $name = 'Friendlink';

    public function addFlink($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        $id = M('Friendlink')->add($data);
        //更新附件信息
        $key = 'Friendlink/'.$id;
        $this->bindAttach($key);
        return $id;
    }

    public function updateFlink($data) {
        $id = $data['id'];
        $data['updatetime'] = time();
        $result = M('Frindlink')->save($data);
        //更新附件信息
        $key = 'Friendlink/'.$id;
        $this->bindAttach($key);
        return $result;
    }

    public function getFlinks($where, $order="id DESC", $limit=10) {
        return M('Friendlink')->where($where)->order($order)->limit($limit)->select();
    }

    public function getFlinksByType($key) {
        $type = D('Flinktype','Service')->getType($key);
        if ($type) {
            $catid = $type['id'];
            $where = array('catid' => array('eq', $catid));
            return $this->getFlinks($where);
        } else {
            return false;
        }
    }

    public function getFlink($key) {
        if (is_numeric($key)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = array($mk=>array('eq', $key));
        return M('Friendlink')->where($where)->find();
    }

    public function delFlink($id) {
        $key = "Friendlink/$id";
        D('Attachment','Service')->delAttachment($key);
        M('Friendlink')->delete($id);
    }

    public function delFlinks($where) {
        M('Friendlink')->where($where)->delete();
    }
}