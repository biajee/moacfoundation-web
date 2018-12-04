<?php
namespace Admin\Controller;
class EventsController extends BaseController {
    public function index() {
        $model = M('Events');
        $where = array();
        $title = I('title');
        if (!empty($title))
            $where['title'] = array('like',"%{$title}%");
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = I('order');
        if (empty($order))
            $order = 'id DESC';
        $list = $model->where($where)->limit($limit)->order($order)->select();
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
        $this->assign('caption','Events List');
        $this->display();
    }

    public function add() {
        $model = M('Info');
        $id = I('id');
        $data = $model->find($id);
        $this->prepare();
        $data = array(
            'hash' => uniqid2()
        );
        $this->assign('data', $data);
        $this->assign('caption', 'Add Events');
        $this->display();
    }

    public function edit() {
        $model = M('Events');
        $id = I('id');
        $data = $model->find($id);
        $this->prepare();
        $this->assign('data', $data);
        $this->assign('caption', 'Edit Events');
        $this->display();
    }
    public function save() {
        $model = M('Events');
        $data = $model->create();
        if (empty($data['title']))
            $this->error('请输入标题');
        $data['status'] = 1;
        $model->add($data);
        $this->success('Success',U('Events/index'));
    }

    public function update() {
        $model = M('Events');
        $data = $model->create();
        if (empty($data['title']))
            $this->error('请输入标题');
        $model->save($data);
        $this->success('Success',U('Events/index'));
    }
    public function detail() {
        $id = I('id');
        $model = D('Info');
        $data = $model->find($id);
        $this->formatData($data);
        $this->assign('data', $data);
        $this->assign('caption', '查看详情');
        $this->display();
    }
    
    public function del() {
        $id = I('id');
        D('Events')->delete($id);
        $this->success('Success', U('Events/index'));
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