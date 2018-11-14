<?php
namespace Common\Service;

class AttachmentService
{
    public function attachTo($hash, $key)
    {
        M('Attachment')->where("hash='$hash'")->save(array('mkey' => $key));
    }

    public function delAttachments($key = '')
    {
        $model = M('Attachment');
        if ($key)
            $where = "mkey='$key'";
        else
            $where = "mkey IS NULL";
        $list = $model->where($where)->select();
        foreach ($list as $v) {
            $file = './Upload/' . $v['savepath'] . $v['savename'];
            unlink($file);
        }
        $model->where($where)->delete();
    }
}