<?php
namespace Admin\Controller;
class ListController extends BaseController {
    protected $modelList = array(
        'map' => '一般',
        'range' => '范围',
        'list' => '二级'
        );
    public function index() {
        $model = D('Listtype');
        $where = array();
        $keyword = I('keyword');
        if (!empty($keyword)) {
            $where['title'] = array('like', "%$keyword%");
        }
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        foreach($list as $k => $v) {
            $list[$k]['modelstr'] = $this->modelList[$v['model']];
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理列表');
        $this->display();
    }
    
    public function add() {
        $data = array(
            'hash'=> uniqid2(),
            'sortno' => 0,
        );
        $this->assign('data', $data);
        $this->prepare();
        $this->assign('caption','增加列表');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = M('Listtype');
        $data = $model->find($id);
        $where = array(
            'catid' => array('eq', $id)
        );
        $blist = M('Listitem')->where($where)->order('id asc')->select();
        $this->assign('itemList', $blist);
        $this->assign('data', $data);
        $this->assign('caption', '编辑列表');
        $this->prepare();
        $this->display();
    }
    
    public function update() {
        $model = D('Listtype');
        $data = $model->create();
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        //处理列表项
        $blist = $_POST['items'];
        $itemModel = M('Listitem');
        foreach($blist as $k => $v) {
            if( empty($v['title']))
                continue;
            $item = $v;
            $item['catid'] = $data['id'];
            if (!empty($v['id'])) { //新增
                if (empty($v['del'])) { //修改
                    $itemModel->save($item);
                } else { //删除
                    $itemModel->delete($v['id']);
                }

            }
        }
        $newlist = $_POST['newitems'];
        if ($newlist) {
            foreach($newlist as $k=>$v) {
                if( empty($v['title']))
                    continue;
                $item = $v;
                $item['catid'] = $data['id'];
                $itemModel->add($item);
            }
        }
        $this->success('保存成功', U('List/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Listtype')->deleteOne($id);
        $this->success('删除成功', U('List/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Listtype');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Listtype')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('List/index'));
    }
    protected function prepare() {

        $this->assign('modelList', $this->modelList);
    }
}