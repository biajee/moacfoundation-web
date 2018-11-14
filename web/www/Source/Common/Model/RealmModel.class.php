<?php
namespace Common\Model;
use Think\Model;
class RealmModel extends Model {
    protected $tableName = "realm";
    public function deleteOne($id) {
        $data = $this->find($id);
        $this->delete($id);
        //处理父类
        if ($data) {
            $upid = $data['upid'];
            if ($upid) {
                $cnt = $this->where("upid='$upid'")->count();
                if ($cnt==0) {
                    $this->where("id=$upid")->save(array('isleaf'=>1));
                }
            }
        }
    }
}