<?php
namespace Admin\Controller;
class QuestionController extends BaseController {
	//问卷列表页面
    public function index() {
        if (empty($order))
            $order = 'id DESC';
        $list = M('Question')->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->prepare();
        $this->assign('orderList', array(
            array('text'=>'发布时间↓', 'value'=>'id DESC'),
            array('text'=>'发布时间↑', 'value'=>'id ASC'),
            array('text'=>'置顶顺序↓', 'value'=> 'istop DESC,id DESC'),
            array('text'=>'热度指数↓', 'value'=> 'heat DESC')
        ));
        $this->prepare();
        $this->assign('caption','问卷列表信息');
        $this->display();
    }
    //问卷管理页面
    public function qa() {
        if (empty($order))
            $order = 'id DESC';
        $list = M('Questiona')
        ->join('edb_question ON edb_questiona.qid=edb_question.id')
        ->join('edb_member ON edb_questiona.uid=edb_member.id')
        ->distinct(true)->field('uid,qid,info_title,title,username')->select();
        $where['uid']= $list[2]['uid'];
        $where['edb_questiona.qid']= $list[2]['qid'];
        $where['info_title']= $list[2]['info_title'];
        $list1 = M('Questiona')->join('edb_questionq ON edb_questiona.aid=edb_questionq.id')
        ->where($where)->field('title,type,answer')->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->prepare();
        $this->assign('orderList', array(
            array('text'=>'发布时间↓', 'value'=>'id DESC'),
            array('text'=>'发布时间↑', 'value'=>'id ASC'),
            array('text'=>'置顶顺序↓', 'value'=> 'istop DESC,id DESC'),
            array('text'=>'热度指数↓', 'value'=> 'heat DESC')
        ));
        $this->prepare();
        $this->assign('caption','问卷信息');
        $this->display();
    }
	//新增问卷页面
    public function add() {
        $model = M('Question');
        $id = I('id');
        $data = $model->find($id);
        $this->prepare();
        $data = array(
            'hash' => uniqid2()
        );
        $this->assign('data', $data);
        $this->assign('caption', '增加信息');
        $this->display();
    }
    //新增问卷标题页面
    public function add_q() {
        $model = M('Question');
        $id = I('id');
        $this->assign('id', $id);
        $this->assign('caption', '增加信息');
        $this->display();
    }
    //新增标题选项页面
    public function add_o() {
        $model = M('Questiono');
        $id = I('id');
        $this->assign('id', $id);
        $this->assign('caption', '增加信息');
        $this->display();
    }

    public function edit() {
        $id = I('id');
        $model = D('Question');
        $data = $model->find($id);
//      $this->formatData($data);
		$this->assign('id', $id);
        $this->assign('data', $data);
        $this->assign('caption', '审核报名表');
        $this->display();
    }
    //编辑问题
    public function edit_q() {
        $id = I('id');
        $model = D('Questionq');
        $data = $model->find($id);
		$this->assign('id', $id);
        $this->assign('data', $data);
        $this->assign('caption', '审核报名表');
        $this->display();
    }
	//新增问卷api
    public function save() {
        $model = M('Question');
        $data = $model->create();
        if (empty($data['title']))
            $this->error('请输入问卷标题');
        $data['addtime'] = time();
        $res = $model->add($data);
        $this->success('添加成功',U('Question/index'));
    }
    //新增问卷api
    public function save_q() {
        $model = M('Questionq');
        $data = $model->create();
        if (empty($data['title']))
            $this->error('请输入问题标题');
        $res = $model->add($data);
        $this->success('添加成功',U('Question/detail?id='.$data['qid']));
    }
    //新增问卷api
    public function save_o() {
        $model = M('Questiono');
        $data = $model->create();
        if (empty($data['text']))
            $this->error('请输入选项内容');
        $res = $model->add($data);
        $this->success('添加成功',U('Question/detail_q?id='.$data['aid']));
    }
    public function delete_q() {
        $data['id'] = I('id');
        $qid = I('qid');
        $model = M('Questionq');
        $data['status'] = 0;
        D('Questionq')->save($data);
        $this->success('删除成功', U('Question/detail?id='.$qid));
    }

    public function update() {
        $model = M('Question');
        $data = $_POST;
        if (empty($data['title']))
            $this->error('请输入标题');
        $data['addtime'] = time();
        $model->save($data);
        $this->success('修改成功',U('Question/index'));
    }
    public function update_q() {
        $model = M('Questionq');
        $qid = I('qid');
        $data = $_POST;
        if (empty($data['title']))
            $this->error('请输入标题');
        $model->save($data);
        $this->success('修改成功',U('Question/detail?id='.$qid));
    }
    //问题详情
    public function detail() {
        $id = I('id');
        $model = M('Questionq');
        $where = array('status'=>1,'qid'=>$id);
        $data = M('Question')->find($id);
        $list = $model->where($where)->order('sortno DESC')->select();
		$this->assign('id', $id);
        $this->assign('data', $data);
        $this->assign('list', $list);
        $this->assign('caption', '查看详情');
        $this->display();
    }
    //问题选项详情
    public function detail_q() {
        $id = I('id');
        $model = M('Questiono');
        $where = array('aid'=>$id);
        $data = M('Questionq')->find($id);
        $list = $model->where($where)->select();
		$this->assign('id', $id);
        $this->assign('data', $data);
        $this->assign('list', $list);
        $this->assign('caption', '查看详情');
        $this->display();
    }
    //答卷页面详情
    public function detail_qa() {
    	$where['uid']= I('uid');
        $where['edb_questiona.qid']= I('qid');
        $where['info_title']= I('info_title');
        $list = M('Questiona')
        ->join('edb_questionq ON edb_questiona.aid=edb_questionq.id')
        ->where($where)->field('title,type,answer')->select();
        foreach($list as $k=>$v){
        	if($list[$k]['type'] == 1){
        		$res = M('Questiono')->where('id='.$list[$k]['answer'])->find();
        		$list[$k]['answer'] = $res['text'];
        	}
        }
        $name = M('Member')->where('id='.$where['uid'])->field('username')->find();
//      var_dump($list);exit;
		$this->assign('info_title', $where['info_title']);
		$this->assign('name', $name['username']);
        $this->assign('data', $data);
        $this->assign('list', $list);
        $this->assign('caption', '查看详情');
        $this->display();
    }
    
    public function delete() {
        $id = I('id');
        D('Info')->deleteOne($id);
        $this->success('删除成功', U('Info/index'));
    }
    public function lock() {
        $id = I('id');
        $model = M('Info');
        $model->where("id=$id")->save(array('status'=>2));
        $this->success('锁定成功', U('Info/index'));
    }
    public function unlock() {
        $id = I('id');
        $model = M('Info');
        $model->where("id=$id")->save(array('status'=>1));
        $this->success('解锁成功', U('Info/index'));
    }
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Info');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Info')->deleteBatch($id);
                break;
            case 'top':
                $model->where("id IN($ids)")->save(array('istop'=>time()));
                break;
            case 'untop':
                $model->where("id IN($ids)")->save(array('istop'=>0));
                break;
            case 'check':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'lock':
                $model->where("id IN($ids)")->save(array('status'=>2));
                break;
            case 'unlock':
                $model->where("id IN($ids)")->save(array('status'=>1));
        }
        $this->success('执行成功',U('Info/index'));
    }

    protected function formatData(&$data) {
        static $district = null;
        static $realm = null;
        $cache = service('Cache');
        if (empty($district)) {
            $district = $cache->getData('DistrictMlt');
        }
        if (empty($realm)) {
            $realm = $cache->getData('RealmMlt');
        }
        $data['countrystr'] = $district[$data['country']]['title'];
        $data['citystr'] = $district[$data['city']]['title'];
        $data['realmstr'] = $realm[$data['realm']]['title'];
        $data['realm2str'] = $realm[$data['realm2']]['title'];
    }
    public function prepare() {
        $this->assign('districtList', $this->cacheSvc->getData('DistrictMlt'));
        $this->assign('realmList', $this->cacheSvc->getData('RealmMlt'));
        $this->assign('statusList', $this->cacheSvc->getData('InfoStatusMlt'));
        $this->assign('moduleList', $this->cacheSvc->getData('InfoModuleMlt'));
    }
}