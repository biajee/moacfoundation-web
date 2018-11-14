<?php
namespace Admin\Controller;
class IssuetypeController extends BaseController {
    public function index() {
        $model = M('Listitem');
        $cond = array(
        	'catid' => '5',
        );
        $list = $model->where($cond)->order('id ASC')->select();
        $this->assign('caption', '文章发布类型管理');
        $this->assign('list', $list);
        $this->display();
    }

    public function tree() {
        $model = M('Listitem');
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
    	//类型功能
    	$action = M('Action')->select();
    	$this->assign('action', $action);
        $levels = array('发布类型','发布类型详情');
        $model = M('Listitem');
        
        $data = array(
            'id'=>'',
            'catid' => '5',
        );
        $title = "发布类型";
        $this->assign('caption',"增加{$title}信息");
        $this->assign('act', 'add');
        $this->assign('data', $data);
        //$this->prepare();
        $this->display('edit');
    }
    
    public function edit() {
    	//类型功能
    	$action = M('Action')->select();
    	$this->assign('action', $action);
        $model = M('Listitem');
        $id = I('id');
        //$this->checkId($id);
        $data = $model->where("id=$id")->find();
        //显示页面
        $this->assign('caption','修改发布类型信息');
        $this->assign('act', 'edit');
        $this->prepare();
        $this->assign('data', $data);
        $this->display();
    }
    
    public function update() {
        $model = M('Listitem');
        $id = I('id');
        $key = I('key');
        if ($data = $model->create()) {
        	$data['catid'] = '5';
            if (empty($id)) {
            	$where = array(
		        	'catid' => '5',
		        	'key' => $key
		        );
		        $res = M('listitem')->where($where)->find();
		        if(!empty($res)){
		        	$this->error('已存在关键词，请更换');
		        }
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
//            $this->error($model->getError());
        }
        $this->ajaxSuccess($data);
    }
    
    public function delete() {
        $id = I('get.id');
        $this->checkId($id);
        $model = M('Listitem');
        $data = $model->find($id);
        $model->delete($id);
        
        //$this->cache();
//       $this->success('删除成功',U('Dynamiclink/index'));
    }
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('Listitem');
    }
    private function prepare() {
    	$list = $this->cacheSvc->getData('RealmMlt');
        $this->assign('typelist', $list);
    	$list = $this->cacheSvc->getData('RealmMlt');
    	$this->assign('levellist', $list);
    }
}