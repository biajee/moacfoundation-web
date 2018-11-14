<?php
namespace Common\Model;
use Think\Model;
class AttachmentModel extends Model {
    public function attachTo($hash, $key) {
        $this->where("hash='$hash'")->save(array('mkey'=>$key));
    }
    
    public function deleteByKey($key='') {
        if ($key)
            $where = "mkey='$key'";
        else
            $where = "mkey IS NULL";
        $list = $this->where($where)->select();
        foreach($list as $v) {
            $file = './Upload/'.$v['savepath'].$v['savename'];
            unlink($file);
        }
        $this->where($where)->delete();
    }
}