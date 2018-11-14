<?php
namespace Common\Model;
use Think\Model;
class RecruitModel extends Model {
    protected $tableName = 'recruit';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}