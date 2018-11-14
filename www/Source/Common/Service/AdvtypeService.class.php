<?php
namespace Common\Service;

/**
 * Class AdvtypeService 广告分组
 * @package Common\Service
 */
class AdvtypeService extends BaseService
{
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