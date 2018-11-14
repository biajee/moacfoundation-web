<?php
namespace Admin\Controller;
class ArticleController extends BaseController {
    protected function _initialize() {
        parent::_initialize();
        $this->perview['promo'] = $this->checkPerview('Article/promo');
        $this->assign('perview', $this->perview);
    }
    public function index() {
        $model = D('Article');
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
        $list = $model->field('id,title,catid,sortno,addtime,status,ishot,updatetime,lastadmname')
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
        $this->assign('caption','管理文章');
        $this->display();
    }
    
    public function add() {
        $this->prepare();
        $data = array(
            'hash'=> uniqid2(),
            'addtime' => time(),
            'updatetime' => time(),
            'sortno' => 0,
            'viewnum' => 0,
            'praisenum' => 0,
            'sharenum' => 0,
            'ishot' => 0
        );
        $this->assign('data', $data);
        $this->assign('caption','增加文章');
        $this->display('edit');
    }
    
    public function edit() {
        $id = I('id');
        $model = D('Article');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->prepare();
        $this->assign('caption', '编辑文章');
        $this->display();
    }
    
    public function update() {
        $model = D('Article');
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
        $data['updatetime'] = time();
        $data['lastadmname'] = $this->admin['username'];
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        //更新附件信息
        $key = 'Article/'.$data['id'];
        D('Attachment')->attachTo($data['hash'],$key);
        
        $this->success('保存成功', $this->getLastUrl());
    }
    
    public function delete() {
        $id = I('id');
        D('Article')->deleteOne($id);
        $this->success('删除成功', U('Article/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Article');
        $ids = implode( ',', $id);
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
                D('Article')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Article/index'));
    }
    
    protected function prepare() {
        $tree = $this->cacheSvc->getData('ArchiveTypeTlt');
        $this->assign('typetree', $tree);
    }
}