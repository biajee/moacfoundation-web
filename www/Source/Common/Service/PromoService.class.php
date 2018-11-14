<?php
namespace Common\Service;
/**
 * Class PromoService 推广服务
 * @package Common\Service
 */
class PromoService {

    public function addPromo($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        return M('Promo')->add($data);
    }

    public function updatePromo($data) {
        $data['updatetime'] = time();
        return M('Promo')->save($data);
    }

    public function getPromos($where, $limit=10, $order="sortno DESC") {
        $list = M('Promo')->where($where)->order($order)->limit($limit)->select();
        return  $list;
    }

    public function getPromosByType($key, $limit=10, $order='id DESC') {
        $type = $this->getType($key);
        if ($type) {
            $catid = $type['id'];
            $where = array('catid' => array('eq', $catid));
            $list = $this->getPromos($where, $limit, $order);
            return $list;
        } else {
            return false;
        }
    }

    public function getPromo($key) {
        if (is_numeric($key)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = array($mk=>array('eq', $key));
        return M('Promo')->where($where)->find();
    }

    public function delPromo($id) {
        $key = "Promo/$id";
        D('Attachment','Service')->delAttachment($key);
        M('Promo')->delete($id);
    }

    public function delPromos($where) {
        M('Promo')->where($where)->delete();
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
        return M('Promotype')->where($where)->find();
    }

    /**
     * 获取列表
     * @param $where
     * @param $order
     * @param $limit
     * @return mixed
     */
    public function getTypes($where, $order, $limit) {
        return M('Promotype')->where($where)->order($order)->limit($limit)->select();
    }

    /**
     * 增加分类
     * @param $data
     * @return int 新建id
     */
    public function addType($data) {
        $data['addtime'] = time();
        return M('Promotype')->add($data);
    }

    /**
     * 更新信息
     * @param $data
     * @return bool 影响行数
     */
    public function updateType($data) {
        return M('Promotype')->save($data);
    }

    /**
     * 删除广告组
     * @param $id
     * @param bool|false $isCascade 是否级联删除广告
     */
    public function delType($id, $isCascade=false) {
        $key = 'Promotype/'.$id;
        D('Attachment','Service')->deleteByKey($key);
        M('Promotype')->delete($id);
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