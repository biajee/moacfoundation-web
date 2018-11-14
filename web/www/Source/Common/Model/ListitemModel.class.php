S<?php
namespace Common\Model;
use Think\Model;
class ListitemModel extends Model {
    protected $tableName = 'listitem';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }

    public function deleteByType($tid) {
        $this->where(array('tourid'=>array('eq', $tid)))->delete();
    }
}