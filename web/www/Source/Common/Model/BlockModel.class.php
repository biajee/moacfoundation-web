<?php
namespace Common\Model;
use Think\Model;

/**
 * Class BlockModel
 * @package Common\Model
 * @author 33hl.cn
 */
class BlockModel extends Model {
    protected $tableName = 'block';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Block/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}