<?php
namespace Common\Service;
class ArctypeService {
    protected  $_list = array();
    public function __construct() {
        $cacheSec = D('Cache','Service');
        //$this->_list = $cacheSec->getData('ArchiveTypeLst');
        $this->_list = M('Arctype')->order('sortno,id')->select();
        foreach($this->_list as $k=>$v) {
            $this->_list[$k]['url'] = $this->getUrl($v);
        }
    }
//根据id或code返回栏目信息
    public function getType($key) {
        if (is_numeric($key)) {
            $mk = 'id';
        } else {
            $mk = 'code';
        }
        foreach($this->_list as $k=>$v) {
            if ($v[$mk]==$key)
                return $v;
        }
        return array();
    }

    public function getUrl($type) {
        if ($type['linktype']!='link')
            return U(str_replace('{cid}',$type['id'], $type['linktpl']));
        else
            return $type['linktpl'];
    }

    public function getFirstChild($upid) {
        foreach ($this->_list as $k=>$v) {
            if ($v['upid']==$upid) {
                return $v;
            }
        }
        return array();
    }
    public function getChildren($upid, $flag='') {
        $list = array();
        foreach ($this->_list as $k=>$v) {
            if ($flag=='menu' && $v['ismenu']==0)
                continue;
            if ($flag=='hot' && $v['ishot']==0) {
                continue;
            }
            if ($v['upid']==$upid) {
                $list[] = $v;
            }
        }
        return $list;
    }
    public function getLeaves($upid) {
        $ids = array();
        foreach($this->_list as $v) {
            if ($v['id']==$upid && $v['isleaf'])
                return $upid;
            if ($v['isleaf'] && strpos($v['upids'], ",$upid,") !== FALSE) {
                $ids[] = $v['id'];
            }
        }
        if ($ids) 
            return implode(',', $ids);
        else
            return '';
    }

    public function getFirstLeaf($upid) {
        foreach($this->_list as $v) {
            if ($v['isleaf'] && strpos($v['upids'], ",$upid,") !== FALSE) {
                return $v['id'];
            }
        }
    }

    public function getTopParent($id) {
        $type = $this->_list[$id];
        $upids = $type['upids'];
        if ($upids) {
            $upids = substr($upids, 1, strlen($upids)-1);
            $ids = explode(',', $upids);
            $topid = $ids[0];
            return $this->_list[$topid];
        }
    }

    public function getPath($id, $hasSelf=true) {
        $type = $this->_list[$id];
        $upids = $type['upids'];
        $ids = array();
        if ($upids) {
            $upids = substr($upids, 1, strlen($upids)-1);
            $ids = explode(',', $upids);
        }
        $result = array();
        foreach ($ids as $v) {
            $result[]= $this->_list[$v];
        }
        if ($hasSelf)
            $result[] = $type;
        return $result;
    }
    
    public function updateStat($id, $item) {
        $model = M('Arctype');
        $data = array('id'=>$id,
                $item => array('exp',"{$item}+1")
            );
        $model->save($data);
        return $model->where(array('id',array('eq', $id)))->getField($item);
    }

}
