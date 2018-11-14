<?php
namespace Admin\Controller;
class DynamiclinkController extends BaseController {
    public function index() {
        $model = D('Dynamiclink');
        $list = $model->order('sortno ASC,id ASC')->select();
        //$list = tree_build($list);
//      $modellist = $this->cacheSvc->getData('RealmMap');
//      foreach($list as $k=>$v) {
//          $list[$k]['modelname'] = $modellist[$v['model']];
//      }
        $this->assign('caption', '动态菜单');
        $this->assign('list', $list);
//      $levels = $this->cacheSvc->getData('RealmMlt');
//      $this->assign('levellist', $levels);
        $this->display();
    }

    public function tree() {
        $model = D('Dynamiclink');
        $upid = I('pid');
        if (empty($upid))
            $upid = 0;
        $list = $model->where("upid=$upid")->order('id ASC')->select();
        //$list = tree_build($list);
        $modellist = $this->cacheSvc->getData('RealmMap');
        foreach($list as $k=>$v) {
            $list[$k]['modelname'] = $modellist[$v['model']];
        }
        $this->assign('caption', '动态菜单');
        $this->assign('list', $list);
        $levels = $this->cacheSvc->getData('RealmMlt');
        $this->assign('levellist', $levels);
        $this->display();
    }
    
    public function batch() {
        $ids = $_POST['id'];
        $sorts = $_POST['sortno'];
        $model = D('Dynamiclink');
        if ($ids) {
            foreach($ids as $id) {
                $sortno = $sorts[$id];
                $model->where("id=$id")->save(array('sortno'=>$sortno));
            }
            $this->success('更新成功',U('Dynamiclink/index'));
        } else {
            $this->error('请选择更新的项目');
        }
    }
    
    public function add() {
        $levels = array('菜单','菜单详情');
        $model = D('Dynamiclink');
        
        $data = array(
            'id'=>'',
            'sortno'=>0,
			'hash'=>uniqid2()
        );
        $title = "菜单";
        $this->assign('caption',"增加{$title}信息");
        $this->assign('act', 'add');
        $this->assign('data', $data);
        //$this->prepare();
        $this->display('edit');
    }
    
    public function edit() {
        $model = D('Dynamiclink');
        $id = I('id');
        //$this->checkId($id);
        $data = $model->where("id=$id")->find();
        //显示页面
        $this->assign('caption','修改菜单信息');
        $this->assign('act', 'edit');
        $this->prepare();
        $this->assign('data', $data);
        $this->display();
    }
    
    public function update() {
        $model = D('Dynamiclink');
        $id = I('id');
        if ($data = $model->create()) {
            if (empty($id)) {
                $data['isleaf'] = 1;
                $model->add($data);
            } else {
                $model->save($data);
            }
            //更新附件信息
            //$key = 'Dynamiclink/'.$data['id'];
            //D('Attachment')->attachTo($data['hash'],$key);
            //缓存
            //$this->cache();
            //$this->redirect('Dynamiclink/index#last');
        } else {
            //$this->error($model->getError());
        }
        $this->ajaxSuccess($data);
    }
    
    public function delete() {
        $id = I('get.id');
        $this->checkId($id);
        $model = D('Dynamiclink');
        $data = $model->find($id);
        $model->delete($id);
        
        //$this->cache();
//       $this->success('删除成功',U('Dynamiclink/index'));
    }
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('Dynamiclink');
    }
    private function prepare() {
    	$list = $this->cacheSvc->getData('RealmMlt');
        $this->assign('typelist', $list);
    	$list = $this->cacheSvc->getData('RealmMlt');
    	$this->assign('levellist', $list);
    }
}