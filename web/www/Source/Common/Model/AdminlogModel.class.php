<?php
namespace Common\Model;
use Think\Model;
class ClientModel extends Model {
    protected $tableName = 'adminlog';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
    public function deleteByItem($type, $itemid) {
        $where = array(
            'module' => array('eq', $type),
            'itemid' => array('eq', $itemid)
        );
        $this->where($where)->delete();
    }
    public function updateStatus($ids, $status) {
        $where = array(
            'id' => array('in', $ids)
        );
        $data = array(
            'status' => $status
        );
        $this->where($where)->save($data);
    }
}