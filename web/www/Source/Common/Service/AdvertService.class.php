<?php
namespace Common\Service;
/**
 * Class AdvertService 广告服务
 * @package Common\Service
 */
class AdvertService {

    public function addAdvert($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Advert')->add($data);
    }

    public function updateAdvert($data) {
        $data['updatetime'] = time();
        return M('Advert')->save($data);
    }

    public function getAdverts($where, $limit=10, $order="sortno DESC") {
        $list = M('Advert')->where($where)->order($order)->limit($limit)->select();
        foreach($list as $k=>$v) {
            $list[$k]['image'] = fix_imgurl($v['image']);
        }
        return  $list;
    }

    public function getAdvertsByType($key, $limit=10, $order='id DESC') {
        $type = $this->getType($key);
        if ($type) {
            $catid = $type['id'];
            $where = array(
                'status' => array('eq', 1),
                'catid' => array('eq', $catid)
                );
            $list = $this->getAdverts($where, $limit, $order);
            return $list;
        } else {
            return false;
        }
    }

    public function getAdvert($key) {
        if (is_numeric($key)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = array($mk=>array('eq', $key));
		$data = M('Advert')->where($where)->find();
		$data['image'] = fix_imgurl($data['image']);
        return $data;
    }

    public function delAdvert($id) {
        $key = "Advert/$id";
        D('Attachment','Service')->delAttachment($key);
        M('Advert')->delete($id);
    }

    public function delAdverts($where) {
        M('Advert')->where($where)->delete();
    }

    /**
     * 获取分类详细信息
     * @param $key
     * @return mixed
     */
    public function getType($key) {
        if (is_int($key))
            $mk = 'id';
        else
            $mk = 'code';
        $where = array(
            $mk => array('eq', $key)
        );
        return M('Advtype')->where($where)->find();
    }

    /**
     * 获取列表
     * @param $where
     * @param $order
     * @param $limit
     * @return mixed
     */
    public function getTypes($where, $order, $limit) {
        return M('Advtype')->where($where)->order($order)->limit($limit)->select();
    }

    /**
     * 增加分类
     * @param $data
     * @return int 新建id
     */
    public function addType($data) {
        $data['addtime'] = time();
        return M('Advtype')->add($data);
    }

    /**
     * 更新信息
     * @param $data
     * @return bool 影响行数
     */
    public function updateType($data) {
        return M('Advtype')->save($data);
    }

    /**
     * 删除广告组
     * @param $id
     * @param bool|false $isCascade 是否级联删除广告
     */
    public function delType($id, $isCascade=false) {
        $key = 'Advtype/'.$id;
        D('Attachment','Service')->deleteByKey($key);
        M('Advtype')->delete($id);
        if ($isCascade) {
            D('Advert','Service')->delAdverts(array('catid'=>array('eq',$id)));
        }
    }

    /**
     * 批量删除广告组
     * @param $ids
     * @param bool|false $isCasecade
     */
    public function delTypes($ids, $isCasecade=false) {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        foreach($ids as $id) {
            $this->delType($id, $isCasecade);
        }
    }
}