<?php
namespace Admin\Controller;
class BlockController extends BaseController {
    public function index() {
        $model = D('Block');
        $where = '1=1';
        $keyword = I('keyword');
        $catid = I('catid');
        $typelist = $this->cacheSvc->getData('BlockTypeMlt');
        if (!empty($catid)) {
            $catid = tree_get_leaves($typelist, $catid);
            $where .= " AND catid IN($catid)";
        }
        if (!empty($keyword))
            $where .= " AND title LIKE '%{$keyword}%'"; 
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('id,title,catid,code,addtime,status')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        foreach($list as $k=>$v) {
            $list[$k]['catname'] = $typelist[$v['catid']]['title'];
        }

        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理版块');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash'=> uniqid2(),
            'addtime' => time(),
        );
        $this->assign('data', $data);
        $this->assign('caption','增加版块');
        $this->prepare();
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Block');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '编辑版块');
        $this->prepare();
        $this->display();
    }
    
    public function update() {
        $model = D('Block');
        $data = $model->create();
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        //更新附件信息
        $key = 'Block/'.$data['id'];
        D('Attachment')->attachTo($data['hash'],$key);
        $this->success('保存成功', U('Block/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Block')->deleteOne($id);
        $this->success('删除成功', U('Block/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
        $model = D('Block');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'hot':
                $model->where("id IN($ids)")->save(array('ishot'=>time()));
                break;
            case 'unhot':
                $model->where("id IN($ids)")->save(array('ishot'=>0));
                break;
            case 'delete':
                D('Block')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Block/index'));
    }
    
    protected function prepare() {
        $tree = $this->cacheSvc->getData('BlockTypeLst');
        $this->assign('typelist', $tree);
    }
}