<?php
namespace Admin\Controller;
class BlocktypeController extends BaseController {
    public function index() {
        $model = D('Blocktype');
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
        $this->assign('caption','管理版块组');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash' => uniqid2()
        );
        $this->assign('data', $data);
        $this->assign('caption','增加版块组');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Blocktype');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑版块组');
        $this->display();
    }
    
    public function update() {
        $model = D('Blocktype');
        $data = $model->create();
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        $key = 'Blocktype/'.$data['id'];
        D('Attachment')->attachTo($data['hash'],$key);
        $this->cache();
        $this->success('保存成功', U('Blocktype/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Blocktype')->deleteOne($id);
        $this->cache();
        $this->success('删除成功', U('Blocktype/index'));
    }
    //刷新缓存
    private function cache() {
        $this->cacheSvc->cacheData('BlockType');
    }
}