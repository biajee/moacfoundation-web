<?php
namespace Common\Model;
use Think\Model;
class ListtypeModel extends Model {
    protected $tableName = 'listtype';
    public function deleteOne($id) {
        $this->delete($id);
        D('Listitem')->deleteByType($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}