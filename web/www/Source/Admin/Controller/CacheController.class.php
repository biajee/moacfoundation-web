<?php
namespace Admin\Controller;
class CacheController extends BaseController {
    public function index() {
        $this->assign('list', $this->cacheSvc->getConfig());
        $this->display();
    }
    
    public function batch() {
        $idArr = $_POST['id'];
        if (empty($idArr))
            $this->error('请选择要刷新的缓存');
        foreach($idArr as $v) {
            $this->cacheSvc->cacheData($v);
        }
        $this->success('缓存已刷新',U('Cache/index'));
    }
    
    public function refresh() {
        $this->cacheSvc->initCache();
        $this->success('缓存已刷新',U('Cache/index'));
    }
}