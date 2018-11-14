<?php
namespace Admin\Controller;
class NotifyController extends BaseController {
    public function index() {
        $this->prepare();
        $this->display();
    }

    public function send() {
        //设置超时
        set_time_limit(60 * 5);
        $model = service('Member');
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
        $where['email'] = array('neq', '');
        $list = $model->getMembers($where, null, 'id ASC', 'id,username,nickname,mobile,email');
        if ($list) {
            $emails = array();
            foreach($list as $v) {
                $emails[] = $v['email'];
            }
            if ($emails) {
                $mailSvc = service('Mail');
                $title = I('title');
                $message = I('message');
                try {
                    $mailSvc->sendBulk($emails, $title, $message);
                    $this->ajaxSuccess(count($emails));
                } catch (\Exception $e) {
                    $this->ajaxError($e->getMessage());
                }

            } else {
                $this->ajaxError('未找到符合条件的会员');
            }
        }  else {
            $this->ajaxError('未找到符合条件的会员');
        }

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
}