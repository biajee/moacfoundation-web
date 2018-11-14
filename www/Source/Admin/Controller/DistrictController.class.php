<?php
namespace Admin\Controller;
class DistrictController extends BaseController {
    public function index() {
        $model = D('District');
        $list = $model->where("upid=0")->order('id ASC')->select();
        //$list = tree_build($list);
        $modellist = $this->cacheSvc->getData('DistrictMap');
        foreach($list as $k=>$v) {
            $list[$k]['modelname'] = $modellist[$v['model']];
        }
        $this->assign('caption', '领域信息');
        $this->assign('list', $list);
        $levels = $this->cacheSvc->getData('DistrictMlt');
        $this->assign('levellist', $levels);
        $this->display();
    }

    public function tree() {
        $model = D('District');
        $upid = I('pid');
        if (empty($upid))
            $upid = 0;
        $list = $model->where("upid=$upid")->order('id ASC')->select();
        //$list = tree_build($list);
        $modellist = $this->cacheSvc->getData('DistrictMap');
        foreach($list as $k=>$v) {
            $list[$k]['modelname'] = $modellist[$v['model']];
        }
        $this->assign('caption', '领域管理');
        $this->assign('list', $list);
        $levels = $this->cacheSvc->getData('DistrictMlt');
        $this->assign('levellist', $levels);
        $this->display();
    }
    
    public function batch() {
        $ids = $_POST['id'];
        $sorts = $_POST['sortno'];
        $model = D('District');
        if ($ids) {
            foreach($ids as $id) {
                $sortno = $sorts[$id];
                $model->where("id=$id")->save(array('sortno'=>$sortno));
            }
            $this->success('更新成功',U('District/index'));
        } else {
            $this->error('请选择更新的项目');
        }
    }
    
    public function add() {
        $model = D('District');
        
        $data = array(
            'id'=>'',
            'sortno'=>0,
			'hash'=>uniqid2()
        );
        $upid = I('upid');
        $title = "";
        if (!empty($upid)) {
            $type = $model->where("id='$upid'")->find();
            $data['upid'] = $upid;
            $levels = $this->cacheSvc->getData('DistrictMlt');
            $title = $levels[$data['level']+1]['title'];
        } else {
            $data['upid'] = 0;
        }
        $this->assign('caption',"增加{$title}信息");
        $this->assign('act', 'add');
        $this->assign('data', $data);
        //$this->prepare();
        $this->display('edit');
    }
    
    public function edit() {
        $model = D('District');
        $id = I('id');
        $this->checkId($id);
        $data = $model->where("id=$id")->find();
        //显示页面
        $this->assign('caption','修改栏目');
        $this->assign('act', 'edit');
        $this->prepare();
        $this->assign('data', $data);
        $this->display();
    }
    
    public function update() {
        $model = D('District');
        $id = I('id');
        if ($data = $model->create()) {
            if (empty($data['ishot'])) {
                $data['ishot'] = 0;
            }
            $upid = $data['upid'];
            if ($upid>0) {
                $uptype = $model->where("id=$upid")->find();
                $data['level'] = $uptype['level'] + 1;
                if (empty($uptype['upids']))
                    $data['upids'] = ",$upid,";
                else
                    $data['upids'] = $uptype['upids'] . "$upid,";
                $model->where("id=$upid")->save(array('isleaf'=>0));
            } else {
                $data['level'] = 0;
                $data['upids'] = '';
            }
            if (empty($id)) {
                $data['isleaf'] = 1;
                $model->add($data);
            } else {
                $model->save($data);
                //是否更改了上级栏目
                $oldupid = I('oldupid');
                if ($oldupid!=$upid) {
                    $cnt = $model->where("upids LIKE '%,$oldupid,%'")->count();
                    if ($cnt<=0) {
                        $model->where("id='$oldupid'")->save(array('isleaf'=>1));
                    }
                    //处理子栏目
                    $list = $model->where("upids LIKE '%,$id,%'")->select();
                    foreach($list as $v) {
                        $save = array();
                        if ($oldupid==0 && $upid>0) {
                            $save['level'] = array('exp','level+1');
                            $save['upids'] = ",$upid".$v['upids'];
                        }
                        if ($oldupid>0 && $upid==0) {
                            $save['level'] = array('exp','level-1');
                            $save['upids'] = str_replace(",$oldupid,", ',', $v['upids']);
                        }
                        if ($oldupid>0 && $upid>0) {
                            $save['upids'] = str_replace(",$oldupid,", ",$upid,", $v['upids']);
                        }
                        $model->where("id='$v[id]'")->save($save);
                    }
                }
            }
            //更新附件信息
            //$key = 'Realm/'.$data['id'];
            //D('Attachment')->attachTo($data['hash'],$key);
            //缓存
            //$this->cache();
            //$this->redirect('Realm/index#last');
        } else {
            //$this->error($model->getError());
        }
        $this->ajaxSuccess($data);
    }
    
    public function delete() {
        $id = I('get.id');
        $this->checkId($id);
        $model = D('District');
        $data = $model->find($id);
        //是否可以删除
        if ($data['isleaf']==0) {
            $this->error('该栏目下有子栏目，不可删除');
        }
        $model->deleteOne($id);
        
        //$this->cache();
        //$this->success('删除成功',U('District/index'));
    }
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('District');
    }
    private function prepare() {
    	$list = $this->cacheSvc->getData('DistrictMlt');
        $this->assign('typelist', $list);
    	$list = $this->cacheSvc->getData('DistrictMlt');
    	$this->assign('levellist', $list);
    }
}