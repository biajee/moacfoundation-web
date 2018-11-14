<?php
namespace Admin\Controller;
class AdvertController extends BaseController {
    public function index() {
        $model = D('Advert');
        $where = '1=1';
        $keyword = I('keyword');
        $catid = I('catid');
        $typelist = $this->cacheSvc->getData('AdvertTypeLst');
        $typemap = $this->cacheSvc->getData('AdvertTypeMlt');
        if (!empty($catid)) {
            $catid = tree_get_leaves($typelist, $catid);
            $where .= " AND catid IN($catid)";
        }
        if (!empty($keyword))
            $where .= " AND title LIKE '%{$keyword}%'";
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('id,title,code,catid,addtime,status,updatetime')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();

        foreach($list as $k=>$v) {
			if (!empty($v['catid'])) {
				$type = $typemap[$v['catid']];
				$list[$k]['catname'] = $type['title'].'[' . $type['code'] . ']';
			} else {
				$list[$k]['catname'] = '';
			}
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->prepare();
        $this->assign('caption','管理广告');
        $this->display();
    }

    public function add() {
        $this->prepare();
        $data = array(
            'hash'=> uniqid2(),
            'addtime' => time(),
            'updatetime' => time(),
            'status' => 1
        );
        $this->assign('data', $data);
        $this->assign('caption','增加广告');
        $this->display('edit');
    }

    public function edit() {
        $id = I('id');
        $model = D('Advert');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->prepare();
        $this->assign('caption', '编辑广告');
        $this->display();
    }

    public function update() {
        $model = D('Advert');
        $data = $model->create();
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        $data['updatetime'] = time();
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        //更新附件信息
        $key = 'Advert/'.$data['id'];
        D('Attachment')->attachTo($data['hash'],$key);

        $this->success('保存成功', U('Advert/index'));
    }

    public function delete() {
        $id = I('id');
        D('Advert')->deleteOne($id);
        $this->success('删除成功', U('Advert/index'));
    }

    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Advert');
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
                D('Advert')->deleteBatch($id);
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Advert/index'));
    }

    protected function prepare() {
        $tree = $this->cacheSvc->getData('AdvertTypeLst');
        $this->assign('typelist', $tree);
    }
}
