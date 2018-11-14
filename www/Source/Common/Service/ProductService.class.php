<?php
namespace Common\Service;
class ProductService extends BaseService {

	/**
	 * 添加产品
	 * @return int 产品id
	 */
	public function addProduct() {
		if (empty($data['addtime']))
			$data['addtime'] = time();
		$id = M('Product')->add($data);
		//更新附件信息
		$key = 'Product/'.$id;
		$this->bindAttach($key);
		return $id;
	}

	/**
	 * 更新产品
	 * @param $data 保存的数据
	 * @return int 影响条数
	 */
	public function updateProduct($data) {
		if (empty($data['id'])) {
			E('信息不存在');
		}
		$data['updatatime'] = time();
		$result = M('Product')->save($data);
		$key = 'Product/'.$data['id'];
		$this->bindAttach($key);
		return $result;
	}

	/**
	 * 获取产品列表
	 * @param null $where 条件
	 * @param string $order 排序
	 * @param string $limit 条数
	 * @return mixed
	 */
	public function getProducts($where=null, $limit=10, $order='id DESC') {
		return M('Product')->field('id,title,image,memo,addtime,viewnum,praisenum,sharenum')
				->where($where)
				->order($order)
				->limit($limit)
				->select();
	}
	
	public function getProduct($id) {
		return M('Product')->find($id);
	}
	
	public function updateStat($id, $item) {
        $model = M('Product');
        $data = array('id'=>$id,
                $item => array('exp',"{$item}+1")
            );
        $model->save($data);
        return $model->where(array('id',array('eq', $id)))->getField($item);
    }
}