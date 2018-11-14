<?php
namespace Common\Model;
use Think\Model;

/**
 * Class CommentModel
 * @package Common\Model
 * @author 33hl.cn
 */
class CommentModel extends Model {
    protected $tableName = 'comment';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}