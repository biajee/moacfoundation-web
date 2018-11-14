<?php
namespace Common\Model;
use Think\Model;
class OrderModel extends Model {
    protected $tableName = 'order';
    public function deleteOne($id) {
        $this->delete($id);
        M('Orderdetail')->where("orderid='$id'")->delete();
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}