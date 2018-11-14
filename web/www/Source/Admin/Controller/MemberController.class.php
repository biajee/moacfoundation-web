<?php
namespace Admin\Controller;
class MemberController extends BaseController {
    public function index() {
        $model = service('Member');
        $statuslist = $this->cacheSvc->getData('UserStatusLst');
        $username = I('username');
        if (!empty($username)) {
            $where['username'] = array('like', "%$username%");
        }
        $nickname = I('nickname');
        if (!empty($nickname)) {
            $where['nickname'] = array('like', "%$nickname%");
        }
        $mobile = I('mobile');
        if (!empty($mobile)) {
            $where['mobile'] = array('like', "%$mobile%");
        }
        $email = I('email');
        if (!empty($email)) {
            $where['email'] = array('like', "%$email%");
        }
        //状态
        $status = I('status');
        if ($status !== '') {
            $where['status'] = array('eq', $status);
        }
        //类型
        $key = 'mytype';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
        }
        //实体
        $key = 'entity';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
        }
        //关系
        $key = 'relation';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
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
        //主要领域
        $key = 'realm';
        $val = I($key);
        if (!empty($val)) {
            $where[$key] = array('eq', $val);
        }
        $denylogin = I('denylogin');
        if (!empty($denylogin)) {
            $where['denylogin'] = array('eq', 1);
        }
        $denyinfo = I('denyinfo');
        if (!empty($denyinfo)) {
            $where['denyinfo'] = array('eq', 1);
        }
        $denytrade = I('denytrade');
        if (!empty($denytrade)) {
            $where['denytrade'] = array('eq', 1);
        }

        $order = I('order');
        if (empty($order)) {
            $order = 'id DESC';
        }
        $count = $model->getMemberCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $list = $model->getMembers($where, $limit, $order);
        $statusList = $this->cacheSvc->getData('MemberStatusMlt');
        foreach ($list as &$v) {
            $v['stats'] = $model->getStats($v['id']);
            $v['statusstr'] = $statusList[$v['status']]['title'];
            $v['nickname'] = $v['nickname2'];
        }
        $this->assign('multi', $multi);
        $this->assign('statuslist',$statuslist);
        $this->assign('list', $list);
        $this->prepare();
        $this->assign('orderList', array(
            array('text'=>'注册时间↓', 'value'=>'id DESC'),
            array('text'=>'注册时间↑', 'value'=>'id ASC'),
            array('text'=>'发布数量↓', 'value'=>'infonum DESC'),
            array('text'=>'服务数量↓', 'value'=>'tradenum DESC'),
            array('text'=>'推荐数量↓', 'value'=>'invitenum DESC'),
            array('text'=>'粉丝数量↓', 'value'=>'fansnum DESC'),
        ));
        $this->display();
    }
    
    public function add() {
        $data = array();
        $referee = I('referee');
        if ($referee) {
            $data['referee'] = $referee;
        }
        $this->assign('data', $data);
        $this->prepare();
        $this->display();
    }
    
    public function edit() {
        $id = $_REQUEST['id'];
        $model = service('Member');
        $data = $model->getMember($id);
        $data['nickname'] = $data['nickname2'];
        $this->assign('data', $data);
        $this->prepare();
        $this->display();
    }

    public function detail() {
        $id = $_REQUEST['id'];
        $model = service('Member');
        $data = $model->getMember($id);
        $data['onlinepay'] = $model->getOnlinepay($id);
        $data['bankcard'] = $model->getBankcard($id);
        $data['stats'] = $model->getStats($id);
        /*if ($data['isindauth']) {
            $data['indauth'] = $model->getIndauth($id);
        }
        if ($data['isinsauth']) {
            $data['insauth'] = $model->getInsauth($id);
        }*/
        $this->assign('data', $data);
        $this->display();
    }
    
    public function save() {
        $model = service('Member');
        $data = $model->buildMember();
        $id = I('id');
        $password = I('password');
        $username = I('username');
        //$password = pass_encode($username,$password);
        if ($model->exists('username', $username))
            $this->error('用户名已存在');
        if ($data['mobile']) {
            if (empty($data['countrycode']))
                $this->error('请输入国家区号');
            if ($model->exists('mobile', $data['mobile']))
                $this->error('手机号已存在');
        }

        if ($data['email'] && $model->exists('email', $data['email'], $id))
            $this->error('Email已存在');
        if (empty($data['denylogin']))
            $data['denylogin'] = 0;
        if (empty($data['denypublish']))
            $data['denypublish'] = 0;
        if (empty($data['denytrade']))
            $data['denytrade'] = 0;
        if (empty($data['ismobileauth']))
            $data['ismobileauth'] = 0;
        if (empty($data['ismobileauth']))
            $data['ismobileauth'] = 0;
        if (empty($data['isindauth']))
            $data['isindauth'] = 0;
        if (empty($data['isinsauth']))
            $data['isinsauth'] = 0;
        if (empty($data['isbankauth']))
            $data['iabankauth'] = 0;
        if (empty($data['istop']))
            $data['istop'] = 0;
        $data['password'] = $password;
        $data['addtime'] = time();
        $data['status'] = 1;
        $referee = I('referee');
        if ($referee) {
            $reuser = $model->getMember($referee);
            if ($reuser) {
                $data['refereeid'] = $reuser['id'];
                $data['refereename'] = $reuser['username'];
            }
        }
        try {
           $data['id'] = $model->addMember($data);
           $this->afterRegister($data);
            $this->success('增加成功',U('Member/index'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }
    
    public function update() {
        $model = service('Member');
        $id = I('id');
        $data = $model->buildMember();
        $password = I('password');
        $info = $model->getMember($id);
        /*if (empty($data['mobile']))
            unset($data['mobile']);
        if (empty($data['email']))
            unset($data['email']);*/
        if (!empty($password)) {
            //$username = $info['username'];
            //$password = pass_encode($username,$password);
            $data['password'] = $password;
        } else {
            unset($data['password']);
        }
        if (empty($data['denylogin']))
            $data['denylogin'] = 0;
        if (empty($data['denypublish']))
            $data['denypublish'] = 0;
        if (empty($data['denytrade']))
            $data['denytrade'] = 0;
        if (empty($data['ismobileauth']))
            $data['ismobileauth'] = 0;
        if (empty($data['ismobileauth']))
            $data['ismobileauth'] = 0;
        if (empty($data['isindauth']))
            $data['isindauth'] = 0;
        if (empty($data['isinsauth']))
            $data['isinsauth'] = 0;
        if (empty($data['isbankauth']))
            $data['iabankauth'] = 0;
        if (empty($data['istop']))
            $data['istop'] = 0;
        //不允许修改推荐人，但允许设置
        if ($info['refereeid']) {
            unset($data['refereeid']);
            unset($data['refereename']);
        } else {
            $referee = I('referee');
            if ($referee) {
                $reuser = $model->getMember($referee);
                if ($reuser) {
                    $data['refereeid'] = $reuser['id'];
                    $data['refereename'] = $reuser['username'];
                }
            }
        }
        $model->updateMember($data);
        //处理推荐人
        if ($data['refereeid']) {
            $data['username'] = $info['username'];
            $this->setReferee($data);
        }
        $this->success('修改成功',U('Member/index'));
    }
    
    public function delete() {
        $id = I('id');
        $model = M('Member');
        $model->where("id=$id")->delete();
        $this->success('删除成功',U('Member/index'));
    }

    public function lock() {
        $id = I('id');
        $model = M('Member');
        $model->where("id=$id")->save(array('status'=>2));
        $this->success('锁定成功', U('Member/index'));
    }

    public function unlock() {
        $id = I('id');
        $model = M('Member');
        $model->where("id=$id")->save(array('status'=>1));
        $this->success('锁定成功', U('Member/index'));
    }

    public function batch() {
        $id = $_POST['id'];
        $ids = implode(',', $id);
        $op = $_POST['op'];
        $model = M('Member');
        switch($op) {
            case 'check':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'lock':
                $model->where("id IN($ids)")->save(array('status'=>2));
                break;
            case 'unlock':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'delete':
                $model->where("id IN($ids)")->delete();
                break;
        }
        $this->success('操作成功',U('Member/index'));
    }
    
    public function check() {
        $username = I('param');
        $model = M('Member');
        $cnt = $model->where("username='$username'")->count();
        if ($cnt>0)
            $data = array('info'=>'用户名已存在','status'=>'n');
        else
            $data = array('info'=>'可以使用','status'=>'y');
        $this->ajaxReturn($data);    
    }
    //列表数据
    public function prepare() {
        $this->assign('districtList', $this->cacheSvc->getData('DistrictMlt'));
        $this->assign('realmList', $this->cacheSvc->getData('RealmMlt'));
        $this->assign('typeList', $this->cacheSvc->getData('MemberTypeMlt'));
        $this->assign('entityList', $this->cacheSvc->getData('MemberEntityMlt'));
        $this->assign('relationList', $this->cacheSvc->getData('MemberRelationMlt'));
        $this->assign('statusList', $this->cacheSvc->getData('MemberStatusMlt'));
    }
    //信誉
    public function review() {
        $model = service('Review');
        $uid = I('uid');
        $where = array(
            'otherid' => array('eq', $uid)
        );
        $count = $model->getReviewCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = 'id DESC';
        $list = $model->getReviews($where, $limit, $order);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }
    //推荐人
    public function referee() {
        $model = service('Member');
        $uid = I('uid');
        $where = array(
            'refereeid' => array('eq', $uid)
        );
        $count = $model->getMemberCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = 'id DESC';
        $list = $model->getMembers($where, $limit, $order);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }
    //关注的人
    public function follow() {
        $model = service('Follow');
        $uid = I('uid');
        $where = array(
            'memid' => array('eq', $uid)
        );
        $count = $model->getFollowCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = 'id DESC';
        $list = $model->getFollows($where, $limit, $order);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }
    //粉丝
    public function funs() {
        $model = service('Follow');
        $uid = I('uid');
        $where = array(
            'otherid' => array('eq', $uid)
        );
        $count = $model->getFollowCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = 'id DESC';
        $list = $model->getFollows($where, $limit, $order);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }
    //收藏
    public function favorite() {
        $model = service('Favorite');
        $uid = I('uid');
        $where = array(
            'otherid' => array('eq', $uid)
        );
        $count = $model->getFavoriteCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = 'id DESC';
        $list = $model->getFavorites($where, $limit, $order);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }
    public function comment() {
        $model = service('Comment');
        $uid = I('uid');
        $where = array(
            'memid' => array('eq', $uid)
        );
        $count = $model->getCommentCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = 'id DESC';
        $list = $model->getComments($where, $limit, $order);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }

    public function message() {
        $model = service('Chat');
        $uid = I('uid');
        $where = array(
            'fromid' => array('eq', $uid),
            'toid' => array('eq', $uid),
            '_logic' => 'or'
        );
        $count = $model->getMessageCount($where);
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $limit = $pager->firstRow.','.$pager->listRows;
        $order = 'id DESC';
        $list = $model->getMessages($where, $limit, $order);
        $this->assign('list', $list);
        $this->assign('multi', $multi);
        $this->display();
    }

    public function search() {
        $q = I('q');
        $model = service('Member');
        if ($q) {
            $data = $model->searchMember($q);
            if ($data) {
                $this->ajaxSuccess($data);
            } else {
                $this->ajaxError('未找到符合条件的会员信息');
            }

        } else {
            $this->error('请输入搜索关键字');
        }
    }

    public function info() {
        $id = I('id');
        $data = service('Member')->getMember($id);
        if ($data) {
            $this->ajaxSuccess($data);
        } else {
            $this->ajaxError('会员信息不存在');
        }

    }

    protected function afterRegister($user) {

        $this->setReferee($user);
        $this->notify($user['id'], '', 'register_welcome', null, 'chat');
    }

    protected function setReferee($user) {
        if ($user['refereeid']) { //推荐积分
            $model = service('Member');
            $credit = C('CREDIT_REFEREE');
            if (empty($credit))
                $credit = 10;
            $memo = L('credit_referee', array('name'=> $user['username']));
            $model->addCredit($user['refereeid'], $credit, $memo);
            //更新统计
            service('Member')->updateStat($user['refereeid'], 'invitenum');
        }
    }
}