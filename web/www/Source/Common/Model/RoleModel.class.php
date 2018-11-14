<?php
namespace Common\Model;
use Think\Model;

/**
 * Class RoleModel
 * @package Common\Model
 * @author 33hl.cn
 */
class RoleModel extends Model {
    protected $tableName = 'role';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}