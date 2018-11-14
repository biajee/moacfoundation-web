<?php
namespace Admin\Controller;
class FlinktypeController extends BaseController {
    public function index() {
        $model = D('Advtype');
        $where = '1=1';
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理广告组');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash' => uniqid2()
        );
        $this->assign('data', $data);
        $this->assign('caption','增加广告组');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Advtype');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑广告组');
        $this->display();
    }
    
    public function update() {
        $model = D('Advtype');
        $data = $model->create();
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        $key = 'Advtype/'.$data['id'];
        D('Attachment')->attachTo($data['hash'],$key);
        $this->cache();
        $this->success('保存成功', U('Advtype/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Advtype')->deleteOne($id);
        $this->cache();
        $this->success('删除成功', U('Advtype/index'));
    }
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('AdvertType');
    }
}