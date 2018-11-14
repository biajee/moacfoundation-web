<?php
namespace Common\Model;
use Think\Model;

/**
 * Class CreditModel
 * @package Common\Model
 * @author 33hl.cn
 */
class CreditModel extends Model {
    protected $tableName = 'credit';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}