<?php
namespace Common\Model;
use Think\Model;
class AdvtypeModel extends Model {
    protected $tableName = 'advtype';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Advtype/'.$id;
        D('Attachment')->deleteByKey($key);
        D('Advert')->deleteByType($id);
    }
}