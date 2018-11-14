<?php
namespace Common\Model;
use Think\Model;
class AdvertModel extends Model {
    protected $tableName = 'advert';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Advert/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }

    public function deleteByType($catid) {
        $where = array('catid'=>array('eq', $catid));
        $list = $this->field('id')->where($where)->select();
        foreach($list as $v) {
            $this->deleteOne($v['id']);
        }
    }
}