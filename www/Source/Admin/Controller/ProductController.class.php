<?php
namespace Admin\Controller;
class ProductController extends BaseController {
    public function index() {
        $model = D('Product');
        $where = '1=1';
        $keyword = I('keyword');
        $catid = I('catid');
        $typemap = $this->cacheSvc->getData('ArchiveTypeMap');
        if (!empty($catid)) {
            $catid = $this->arctypeSvc->getLeaves($catid);
            $where .= " AND catid IN($catid)";
        }
        if (!empty($keyword))
            $where .= " AND title LIKE '%{$keyword}%'"; 
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('id,title,catid,brand,spec,status,ishot,addtime,updatetime')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        foreach($list as $k=>$v) {
            $list[$k]['catname'] = $typemap[$v['catid']];
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->prepare();
        $this->assign('caption','管理产品信息');
        $this->display();
    }
    
    public function add() {
        $this->prepare();
        $data = array(
            'hash'=> uniqid2(),
            'price' => 0,
            'mktprice' => 0,
            'addtime' => time(),
            'updatetime' => time(),
            'sortno' => 0,
            'viewnum' => 0,
            'praisenum' => 0,
            'sharenum' => 0,
            'isgood' => 0,
            'ishot' => 0
        );
        $this->assign('data', $data);
        $this->assign('caption','增加产品信息');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Product');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->prepare();
        $this->assign('caption', '编辑产品信息');
        $this->display();
    }
    
    public function update() {
        $model = D('Product');
        $data = $model->create();
        $relation = $_POST['relation'];
        $relation = ext_implode(',', $relation);
        $data['relation'] = $relation;
        if (empty($data['memo'])) {
            $data['memo'] = msubstr(html2text($data['content']),0,500);
        }
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        if (empty($data['price']))
            $data['price'] = 0;
        if (empty($data['mktprice']))
            $data['mktprice'] = 0;
        $data['updatetime'] = time();
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        //更新附件信息
        $key = 'Product/'.$data['id'];
        D('Attachment')->attachTo($data['hash'],$key);
        
        $this->success('保存成功', U('Product/index'));
    }
    
    public function delete() {
        $id = I('id');
        D('Product')->deleteOne($id);
        $this->success('删除成功', U('Product/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Product');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'good':
                $model->where("id IN($ids)")->save(array('isgood'=>time()));
                break;
            case 'ungood':
                $model->where("id IN($ids)")->save(array('isgood'=>0));
                break;
            case 'hot':
                $model->where("id IN($ids)")->save(array('ishot'=>time()));
                break;
            case 'unhot':
                $model->where("id IN($ids)")->save(array('ishot'=>0));
                break;
            case 'delete':
                D('Product')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Product/index'));
    }
    
    protected function prepare() {
        $tree = $this->cacheSvc->getData('ArchiveTypeTlt');
        $this->assign('typetree', $tree);
    }
}