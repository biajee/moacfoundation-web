<?php
namespace Common\Model;
use Think\Model;
class FriendlinkModel extends Model {
    protected $tableName = 'friendlink';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Friendlink/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}