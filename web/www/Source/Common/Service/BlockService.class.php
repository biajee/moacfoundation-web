<?php
namespace Common\Service;
class BlockService {
    public function getType($code) {
        return M('Blocktype')->where("code='$code'")->find();
    }
    
    public function getBlocks($upid) {
        if (!is_numeric($upid)) {
            $type = $this->getType($upid);
            $upid = $type['id'];
            if (empty($upid))
                $upid = 0;
        }
        $where = "catid='$upid'";
        return M('Block')->where($where)->order('sortno,id DESC')->select();
    }

    public function getBlock($id) {
        if (is_numeric($id)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        $where = "$mk='$id'";
        return M('Block')->where($where)->find();
    }
}