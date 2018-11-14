<?php
namespace Common\Model;
use Think\Model;
class EntrustModel extends Model {
    protected $tableName = 'entrust';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}