<?php
namespace Admin\Controller;
class ArctypeController extends BaseController {
    public function index() {
        $model = D('Arctype');
        $list = $model->select();
        $list = tree_build($list);
        $modellist = $this->cacheSvc->getData('ArchiveModelMap');
        foreach($list as $k=>$v) {
            $list[$k]['modelname'] = $modellist[$v['model']];
        }
        $this->assign('caption', '栏目管理');
        $this->assign('list', $list);
        $this->display();
    }
    
    public function batch() {
        $ids = $_POST['id'];
        $sorts = $_POST['sortno'];
        $model = D('Arctype');
        if ($ids) {
            foreach($ids as $id) {
                $sortno = $sorts[$id];
                $model->where("id=$id")->save(array('sortno'=>$sortno));
            }
            $this->success('更新成功',U('Arctype/index'));
        } else {
            $this->error('请选择更新的项目');
        }
    }
    
    public function add() {
        $model = D('Arctype');
        
        $data = array(
            'id'=>'',
            'sortno'=>0,
            'model'=>'default',
            'pagesize'=>20,
            'linktype'=>'index',
            'ishidden'=>0,
            'ismenu'=>1,
            'ishot'=>0,
            'viewnum'=>0,
            'praisenum'=>0,
            'sharenum'=>0,
			'hash'=>uniqid2()
        );
        $upid = I('pid');
        if (!empty($upid)) {
            $type = $model->where("id='$upid'")->find();
            $data['upid'] = $upid;
            $data['model'] = $type['model'];
            $data['linktype'] = $type['linktype'];
            $data['linktpl'] = $type['linktpl'];
            $data['pagesize'] = $type['pagesize'];
            $data['ishidden'] = $type['ishidden'];
            $data['ismenu'] = $type['ismenu'];
            $data['ishot'] = $type['ishot'];
        }
        $this->assign('caption','增加栏目');
        $this->assign('data', $data);
        $this->prepare();
        $this->display('edit');
    }
    
    public function edit() {
        $model = D('Arctype');
        $id = I('id');
        $this->checkId($id);
        $data = $model->where("id=$id")->find();
        //显示页面
        $this->assign('caption','修改栏目');
        $this->prepare();
        $this->assign('data', $data);
        $this->display();
    }
    
    public function update() {
        $model = D('Arctype');
        $id = I('id');
        if ($data = $model->create()) {
            $upid = $data['upid'];
            if (empty($data['ishidden']))
                $data['ishidden'] = 0;
            if (empty($data['ismenu']))
                $data['ismenu'] = 0;
            if (empty($data['ishot']))
                $data['ishot'] = 0;    
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
                        if ($oldid>0 && $upid>0) {
                            $save['upids'] = str_replace(",$oldupids,", ",$upid,", $v['upids']);
                        }
                        $model->where("id='$v[id]'")->save($save);
                    }
                }
            }
            //更新附件信息
            $key = 'Arctype/'.$data['id'];
            D('Attachment')->attachTo($data['hash'],$key);
            //缓存
            $this->cache();
            $this->success('保存成功',U('Arctype/index'));
        } else {
            $this->error($model->getError());
        }
    }
    
    public function delete() {
        $id = I('get.id');
        $this->checkId($id);
        $model = D('Arctype');
        $data = $model->find($id);
        //是否可以删除
        if ($data['isleaf']==0) {
            $this->error('该栏目下有子栏目，不可删除');
        }
        //如果是叶子节点，并且有内容就不可删除
        $typemodel = $data['model'];
        if ($typemodel && $typemodel!='default') {
            $acrmodel = M(ucfirst($typemodel));
            if ($arcmodel) {
                $cnt = $arcmodel->where("catid=$id")->count();
                if ($cnt>0)
                    $this->error('该栏目下有内容，不可删除');
            }
            
        }
        $model->deleteOne($id);
        
        $this->cache();
        $this->success('删除成功',U('Arctype/index'));
    }
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('ArchiveType');
    }
    private function prepare() {
    	$typelist = $this->cacheSvc->getData('ArchiveTypeTlt');
    	$modellist = $this->cacheSvc->getData('ArchiveModelMap');
    	$linktypelist = $this->cacheSvc->getData('LinkTypeMlt');
    	$this->assign('typelist', $typelist);
    	$this->assign('modellist', $modellist);
    	$this->assign('linktypelist', $linktypelist);
    }
}