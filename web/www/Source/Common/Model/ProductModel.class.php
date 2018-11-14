<?php
namespace Common\Model;
use Think\Model;
class ProductModel extends Model {
    protected $tableName = 'product';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Product/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}