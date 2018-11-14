<?php
namespace Common\Model;
use Think\Model;
class MaillistModel extends Model {
    protected $tableName = 'Maillist';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}