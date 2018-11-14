<?php
namespace Admin\Controller;
class BannerController extends BaseController {
    public function index() {
    	$model = D('Banner');
        $list = $model->select();
        $list = tree_build($list);
        $this->assign('caption', 'Banner管理');
        $this->assign('list', $list);
        $this->display();
    }
    
    public function batch() {
        $ids = $_POST['id'];
        $sorts = $_POST['sortno'];
        $model = D('Banner');
        if ($ids) {
            foreach($ids as $id) {
                $sortno = $sorts[$id];
                $model->where("id=$id")->save(array('sortno'=>$sortno));
            }
            $this->success('更新成功',U('Banner/index'));
        } else {
            $this->error('请选择更新的项目');
        }
    }
    
    public function add() {
        $model = D('Banner');
        $data = array(
            'id'=>'',
            'sortno'=>0,
            'hash'=>uniqid2()
        );
        $this->assign('caption','增加banner');
        $this->assign('data', $data);
        $this->display('edit');
    }
    
    public function edit() {
        $model = D('Banner');
        $id = I('id');
        $this->checkId($id);
        $data = $model->where("id=$id")->find();
        //显示页面
        $this->assign('caption','修改banner');
        $this->assign('data', $data);
        $this->display();
    }
    
    public function update() {
        $model = D('Banner');
        $id = I('id');
        if ($data = $model->create()) {
            if (empty($id)) {
            	$data['addtime'] = date('Y-m-d H:i:s',time());
                $model->add($data);
            } else {
                $model->save($data);
            }
            //更新附件信息
            $key = 'Banner/'.$data['id'];
            D('Attachment')->attachTo($data['hash'],$key);
            //缓存
            $this->cache();
            $this->success('保存成功',U('Banner/index'));
        } else {
            $this->error($model->getError());
        }
    }
    public function delete() {
        $id = I('get.id');
        $this->checkId($id);
        $model = D('Banner');
        $data = $model->find($id);
        $model->delete($id);
        //清除缓存
        $key = 'Banner/'.$id;
        D('Attachment')->deleteByKey($key);
        $this->cache();
        $this->success('删除成功',U('Banner/index'));
    }
    
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('Banner');
    }
}