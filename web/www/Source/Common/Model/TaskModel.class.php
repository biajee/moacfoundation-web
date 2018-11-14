<?php
namespace Common\Model;
use Think\Model;

/**
 * Class BlockModel
 * @package Common\Model
 * @author 33hl.cn
 */
class TaskModel extends Model {
    protected $tableName = 'Task';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Task/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}