<?php
namespace Common\Model;
use Think\Model;
class BlocktypeModel extends Model {
    protected $tableName = 'blocktype';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Blocktype/'.$id;
        D('Attachment')->deleteByKey($key);
    }
}