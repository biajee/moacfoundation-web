<?php
namespace Common\Model;
use Think\Model;
class GoodsModel extends Model {
    protected $tableName = 'goods';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Goods/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}