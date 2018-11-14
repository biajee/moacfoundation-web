<?php
namespace Common\Service;

/**
 * Class ResoldquoteService 链接分组
 * @package Common\Service
 */
class ResoldquoteService extends BaseService
{
    /**
     * 获取列表
     * @param $where
     * @param $order
     * @param $limit
     * @return mixed
     */
    public function getQuotes($where, $limit, $order='id DESC') {
        return M('Resoldquote')->where($where)->order($order)->limit($limit)->select();
    }

    public function getQuotesByDistrict($district, $limit, $order='id DESC') {
        $where = array(
            'district' => array('like', "{$district}%")
        );
        $order = 'qyear DESC,qmonth DESC';
        return $this->getQuotes($where, $limit, $order);
    }

    /**
     * 增加分类
     * @param $data
     * @return int 新建id
     */
    public function addQuote($data) {
        $data['addtime'] = time();
        return M('Resoldquote')->add($data);
    }

    /**
     * 更新信息
     * @param $data
     * @return bool 影响行数
     */
    public function updateQuote($data) {
        return M('Resoldquote')->save($data);
    }

    /**
     * 删除广告组
     * @param $id
     * @param bool|false
     */
    public function delType($id) {

        M('Resoldquote')->delete($id);
    }

    /**
     * 批量删除链接分组
     * @param $ids
     * @param bool|false
     */
    public function delTypes($ids) {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        foreach($ids as $id) {
            $this->delType($id);
        }
    }
}