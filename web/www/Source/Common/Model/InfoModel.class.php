<?php
namespace Common\Model;
use Think\Model;

/**
 * Class BlockModel
 * @package Common\Model
 * @author 33hl.cn
 */
class InfoModel extends Model {
    protected $tableName = info;
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'News/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}