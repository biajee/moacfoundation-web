<?php
namespace Admin\Controller;
class ApplyController extends BaseController {
    public function index() {
        $model = service('Apply');
        $where = array();
        $module = I('module');
        if (!empty($module)) {
            $where['module'] = array('eq', $module);
        }
        $title = I('title');
        if (!empty($title))
            $where['title'] = array('like',"%{$title}%");
        if (!empty($module))
            $where['module'] = array('eq', $module);
        $uid = I('uid');
        if ($uid) {
            $where['memid'] = array('eq', $uid);
            //$_GET['username'] = M('Member')->where("id=$uid")->getField('username');
        } else {
            $username = I('name');
            if ($username) {
                if (valid_mobile($username))
                    $where2['mobile'] = array('eq', $username);
                elseif (valid_email($username))
                    $where2['email'] = array('eq', $username);
                else {
                    $where2['name'] = array('eq', $username);
                    $where2['nickname'] = array('eq', $username);
                    $where2['_logic'] = 'or';
                }
                $uids = M('Apply')->where($where2)->getField('id', true);
                if ($uids) {
                    $where['id'] = array('in', $uids);
                } else {
                    $where['id'] = 0;
                }
            }
        }


        $status = I('status');
        if ($status != '') {
            $where['status'] = array('eq', $status);
        }
        //国家
        $key = 'country';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
        }
        //城市
        $key = 'city';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
        }
        //领域
        $key = 'realm';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
        }
        //子领域
        $key = 'realm2';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
        }
        $count = $model->getApplyCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = I('order');
        if (empty($order))
            $order = 'id DESC';
        $list = $model->getApplys($where, $limit, $order);
        foreach($list as &$v) {
            $stat = $model->getStats($v['id']);
            $v['favnum'] = $stat['favnum'];
            $v['reviewnum'] = $stat['reviewnum'];
        }
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
        $this->assign('caption','线上报名信息');
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
        $this->assign('caption', '增加信息');
        $this->display();
    }

    public function edit() {
        $id = I('id');
        $model = D('Apply');
        $data = $model->find($id);
//      $this->formatData($data);
		$this->assign('id', $id);
        $this->assign('data', $data);
        $this->assign('caption', '审核报名表');
        $this->display();
    }
	//修改报名表状态，审核通过
	public function save1(){
		$model = D('Apply');
		if(IS_POST){
			$id = I('id');
			$data['status'] = 1;
			$cond = array(
				'id' => $id,
			);
			
			$res = $model->where($cond)->save($data);
			if($res){
				$msg = array(
					'status' => 1,
					'data' => '',
					'msg' => '审核成功',
				);
				echo json_encode($msg);
				return;
			}else{
				$msg = array(
					'status' => 0,
					'data' => '$id',
					'msg' => '审核失败',
				);
				echo json_encode($msg);
				return;
			}
		}
	}
	
	//修改报名表状态，驳回
	public function save2(){
		$model = D('Apply');
		if(IS_POST){
			$id = I('id');
			$data['status'] = 2;
			$cond = array(
				'id' => $id,
			);
			
			$res = $model->where($cond)->save($data);
			if($res){
				$msg = array(
					'status' => 1,
					'data' => '',
					'msg' => '审核成功',
				);
				echo json_encode($msg);
				return;
			}else{
				$msg = array(
					'status' => 0,
					'data' => '$id',
					'msg' => '审核失败',
				);
				echo json_encode($msg);
				return;
			}
		}
	}
	
    public function save() {
        $model = M('Info');
        $data = $model->create();
        if (empty($data['memid']))
            $this->error('请输入会员信息');
        if (empty($data['title']))
            $this->error('请输入标题');
        $data['addtime'] = time();
        if (empty($data['istop']))
            $data['istop'] = 0;
        if ($data['expire'])
            $data['expire'] = strtotime($data['expire']);
        $data['status'] = 1;
        $model->add($data);
        $this->success('修改成功',U('Info/index'));
    }

    public function update() {
        $model = M('Info');
        $data = $model->create();
        if (empty($data['title']))
            $this->error('请输入标题');
        $data['addtime'] = time();
        if (empty($data['istop']))
            $data['istop'] = 0;
        if ($data['expire'])
            $data['expire'] = strtotime($data['expire']);
        $model->save($data);
        $this->success('修改成功',U('Info/index'));
    }
    public function detail() {
        $id = I('id');
        $model = D('Apply');
        $data = $model->find($id);
//      $this->formatData($data);
		$this->assign('id', $id);
        $this->assign('data', $data);
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