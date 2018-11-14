<?php
namespace Common\Model;
use Think\Model;

/**
 * Class BlockModel
 * @package Common\Model
 * @author 33hl.cn
 */
class TradeModel extends Model {
    protected $tableName = 'Trade';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Trade/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}