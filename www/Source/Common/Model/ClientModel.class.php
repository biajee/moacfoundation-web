<?php
namespace Common\Model;
use Think\Model;
class ClientModel extends Model {
    protected $tableName = 'client';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}