<?php
namespace Common\Service;
/**
 * Class MemberService 会员服务
 * @package Common\Service
 */
class MemberService {
    public function buildMember() {
        return M('Member')->create();
    }
    public function checkUsername($username) {
        if (empty($username) || is_numeric($username)) {
            return false;
        }
        return true;
    }
    /**
     * 获取会员信息
     * @param $where id(必须为整数)或用户名
     * @param $fields
     * @return mixed
     */
    public function getMember($where, $fields='*') {
        if (!is_array($where)) {
			$param = $where;
			$where = array();
            $where['id'] = array('eq', $param);
        }
		$user = M('Member')->where($where)->field($fields)->find();
        if ($user)
            $this->formatData($user);
        return $user;
    }
    //获取是否为商品标识
    public function getIsgoods($where, $fields='*') {
		$user = M('listitem')->where($where)->field($fields)->find();
        return $user['is_goods'];
    }
    public function getMemberById($uid, $fields='*') {
        static $_cache = array();
        if (empty($_cache[$uid])) {
            $_cache[$uid] = $this->getMember($uid, $fields);
        }
        return $_cache[$uid];
    }
    public function getOnlinepay($uid) {
        return M('Onlinepay')->where(array('memid'=>array('eq', $uid)))->select();
    }

    public function getBankcard($uid) {
        return M('bankcard')->where(array('memid'=>array('eq', $uid)))->select();
    }

    public function getStats($uid) {
        $stats['servicenum'] = M('Info')->where("module='service' AND memid=$uid")->count();
        $stats['tasknum'] = M('Info')->where("module='service' AND memid=$uid")->count();
        $stats['newsnum'] = M('Info')->where("module='service' AND memid=$uid")->count();
        $stats['tradenum'] = M('Trade')->where("buyerid=$uid OR sellerid=$uid")->count();
        $stats['refereenum'] = M('Member')->where("refereeid=$uid")->count();
        $stats['mark'] = service('Trade')->getPrestige($uid);
        $stats['follownum'] = M('Follow')->where("memid=$uid")->count();
        $stats['byfollownum'] = M('Follow')->where("otherid=$uid")->count();
        $stats['favnum'] = M('Favorite')->where("memid=$uid")->count();
        $stats['byfavnum'] = M('Favorite')->where("itemowner=$uid")->count();
        $stats['msgnum'] = M('Message')->where("fromid=$uid OR toid=$uid")->count();
        $stats['reviewnum'] = M('Comment')->where("memid=$uid")->count();
        $stats['byreviewnum'] = M('Comment')->where("itemowner=$uid")->count();
        return $stats;
    }

    public function getIndauth($uid) {
        return M('Indauth')->find($uid);
    }

    public function getInsauth($uid) {
        return M('Indauth')->find($uid);
    }

    public function searchMember($keyword) {
        if (valid_mobile($keyword)) {
            $item = 'mobile';
        } elseif (valid_email($keyword)) {
            $item = 'email';
        } else {
            $item = 'username';
        }
        $where[$item] = array('eq', $keyword);
        return $this->getMember($where, 'id,username,mytype,nickname,mobile,email');
    }

	public function getMemberByName($username) {
        $where['username'] = array('eq', $username);
		$user = M('Member')->where($where)->find();
        return $user;
    }
    //获取头像
    public function getAvatar($id) {
        $user = M('Member')->field('avatar')->find();
		$avatar = $user['avatar'];
		if (!empty($avatar)) {
			$avatar = fix_imgurl($user['avatar']);
		}
        return $avatar;
    }

    /**
     * 获取会员列表
     * @param $where 条件
     * @param 条数|int $limit 条数
     * @param 排序|string $order 排序
     * @param 字段|string $field 字段
     * @return mixed 会员列表
     */
    public function getMembers($where, $limit = 0, $order='addtime DESC', $field='*') {
        $model = M('Member');
        $model->field($field)->where($where)->order($order);
        if ($limit)
            $model->limit($limit);
        $list = $model->select();
        if ($list) {
            foreach($list as &$v) {
                if (empty($v['nickname'])) {
                    $v['nickname'] = $v['username'];
                }
                $this->formatData($v);
            }
        }
        return $list;
    }

    public function getMemberCount($where) {
        return M('Member')->where($where)->count();
    }

    public function getTempName($id) {
        return 'hwb'. str_padzero($id, 8);
    }
    /**
     *
     * @param $username
     * @param $password
     * @return mixed
     */
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            E(L('error_login_fail'));
        }
        $realpass = $this->encodePassword($password);
        $model = M('Member');
		$where = array(
			'password' => array('eq', $realpass)
		);
        if (valid_mobile($username)) {
            $where['mobile'] = array('eq', $username);
        } elseif (valid_email($username)) {
            $where['email'] = array('eq', $username);
        } else {
            $where['username'] = array('eq', $username);
        }
        $user = $model->field('id,username')->where($where)->find();
        if (empty($user))
            E(L('error_login_fail'));

        $update = array(
            'id' => $user['id'],
            'lastlogin' => time(),
            'lastip' => get_client_ip(),
            'logintimes' => array('exp', 'logintimes+1')
        );
        $this->updateMember($update);
        tag('member_login', $user);
        return $user;
    }
    //微信登录
    public function login1($username, $password) {
        if (empty($username) || empty($password)) {
            E(L('error_login_fail'));
        }
        $realpass = $this->encodePassword($password);
        $model = M('Member');
		$where = array(
			'password' => array('eq', $password)
		);
        if (valid_mobile($username)) {
            $where['mobile'] = array('eq', $username);
        } elseif (valid_email($username)) {
            $where['email'] = array('eq', $username);
        } else {
            $where['username'] = array('eq', $username);
        }
        $user = $model->field('id,username')->where($where)->find();
        if (empty($user))
            E(L('error_login_fail'));

        $update = array(
            'id' => $user['id'],
            'lastlogin' => time(),
            'lastip' => get_client_ip(),
            'logintimes' => array('exp', 'logintimes+1')
        );
        $this->updateMember($update);
        tag('member_login', $user);
        return $user;
    }

    /**
     * 注册会员
     * @param $data
     * @return int
     */
    public function register($data) {
        if ($this->checkUsername($data['username']))
            E(L('reg_error_invalid_username'));
        if ($this->exists($data['username']))
            E(L('reg_error_username_exists'));
		if ($this->mobileExists($data['countrycode'], $data['mobile']))
			E(L('reg_error_mobile_exists'));
        $data['password'] = pass_encode($data['username'], $data['password']);
        $data['status'] = 1;
        $data['addtime'] = time();
        $result = M('Member')->add($data);
        return $result;
    }

    public function addMember($data) {
        $data['addtime'] = time();
        $data['password'] = $this->encodePassword($data['password']);
        return M('Member')->add($data);
    }

    public function updateMember($data, $where=null) {
        if (!empty($data['password']))
            $data['password'] = $this->encodePassword($data['password']);
        $model = M('Member');
        if ($where)
            $model->where($where);
        return $model->save($data);
    }
	
	public function mobileExists($cc, $mobile, $uid =0) {
        $where['countrycode'] = array('eq', $cc);
		$where['mobile'] = array('eq',$mobile);
		if ($uid)
		    $where['id'] = array('neq', $uid);
        $cnt = M('Member')->where($where)->count();
        if ($cnt>0)
            return true;
        else
            return false;
	}
    //手机号获得用户
    public function getMemberByMobile($mobile) {
        $where = array('mobile'=>array('eq',$mobile));
        $user = $this->getMember($where);
        return $user;
    }
    //自动注册
    public function mobileRegister($mobile) {
        $data = array(
            'username' => $mobile,
            'password' => $mobile,
            'mobile' => $mobile
        );
        $uid = $this->addMember($data);
        $username = 'th'. (123456 + $uid);
        $password = substr($mobile, -8);
        $data = array(
            'id' => $uid,
            'username' => $username,
            'password' => $password,
            'lastlogintime' => time()
        );
        $this->updateMember($data);
        return $uid;

    }
    public function encodePassword($password) {
        return pass_encode('hiwibang', $password);
    }
    public function regByWeixin($data) {
        $data['regby'] = 'weixin';
        if ($data['mytype']>1)
            $data['mytype'] = 0;
        if ($this->exists('username', $data['username'])) {
            E(L('username_exists'));
        }
        $data['status'] = 1;
        $uid = $this->addMember($data);
        return $uid;
    }
    public function regByMobile($data) {
        //$data['regby'] = 'mobile';
        if ($data['mytype']>1)
            $data['mytype'] = 0;
        $data['ismobileauth'] = true;
        if ($this->mobileExists($data['countrycode'], $data['mobile'])) {
            E(L('mobile_exists'));
        }
        if ($this->exists('username', $data['username'])) {
            E(L('username_exists'));
        }
        $data['status'] = 1;
        $uid = $this->addMember($data);
        return $uid;
    }

    public function emailExists($email, $uid=0) {
        $where['email'] = array('eq',$email);
        if ($uid)
            $where['id'] = array('neq', $uid);
        $cnt = M('Member')->where($where)->count();
        if ($cnt>0)
            return true;
        else
            return false;
    }

    public function nicknameExists($nickname) {
        $where['nickname'] = array('eq',$nickname);
        $cnt = M('Member')->where($where)->count();
        if ($cnt>0)
            return true;
        else
            return false;
    }

    public function exists($item, $value, $id = 0) {
        if (!empty($id)) {
            $where['id'] = array('neq', $id);
        }
        $where[$item] = array('eq',$value);
        $cnt = M('Member')->where($where)->count();
        if ($cnt>0)
            return true;
        else
            return false;
    }

    //Email获得用户
    public function getMemberByEmail($email) {
        $where = array('mobile'=>array('eq',$email));
        $user = $this->getMember($where);
        return $user;
    }

    public function regByEmail($data) {
        $data['regby'] = 'email';
        if ($data['mytype']>1)
            $data['mytype'] = 0;
        $data['isemailauth'] = 1;
        if ($this->emailExists($data['email'])) {
            E(L('email_exists'));
        }
        if ($this->exists('username', $data['username'])) {
            E(L('username_exists'));
        }
        $data['status'] = 1;
        $uid = $this->addMember($data);
        return $uid;
    }

    public function changePassword($data) {
        $id = $data['id'];
        $oldpwd = $this->encodePassword($data['oldpwd']);
        $password = $data['password'];
        $where = array(
            'id' => array('eq', $id),
            'password' => $oldpwd
        );
        $cnt = M('Member')->where($where)->count();
        if ($cnt>0) {
            $user = array(
                'id' => $id,
                'password' => $password
            );
            $this->updateMember($user);
        } else {
            E(L('oldpassword_error'));
        }
    }

    public function formatData(& $data) {
        static $district = null;
        static $realm = null;
        $cache = service('Cache');
        if (empty($district)) {
            $district = $cache->getData('DistrictMlt');
        }
        if (empty($realm)) {
            $realm = $cache->getData('RealmMlt');
        }
        $data['nickname2'] = $data['nickname'];
        if (empty($data['nickname'])) {
            $data['nickname'] = $data['username'];
        }
        if ($data['realm'])
            $data['realmstr'] = $realm[$data['realm']]['title'];
        if ($data['country'])
            $data['countrystr'] = $district[$data['country']]['title'];
        if ($data['city'])
            $data['citystr'] = $district[$data['city']]['title'];
        $data['mytypestr'] = L('member_type_'.$data['mytype']);
        $data['relationstr'] = L('member_relation_'.$data['relation']);
        $data['entitystr'] = L('member_entity_'.$data['relation']);

        if ($data['images'])
            $data['imagelist'] = explode(',', $data['images']);
        if ($data['content'] != '') {
            $data['contentfmt'] = nl2br($data['content']);
        }
    }
    public function addCredit($uid, $credit, $memo) {
        $user = $this->getMemberById($uid, 'id,username');
        $update = array(
            'id' => $uid,
            'credit' => array('exp','credit+'.$credit)
        );
        $this->updateMember($update);
        $log = array(
            'memid' => $user['id'],
            'memname' => $user['username'],
            'credit'=> $credit,
            'memo' => $memo
        );
        $this->addCreditLog($log);
    }
    public function addDaybook($data) {
        return M('Daybook')->add($data);
    }

    public function addCreditLog($data) {
        $data['addtime'] = time();
        return M('Credit')->add($data);
    }

    public function updateStat($uid, $item, $num = 1) {
        M('Member')->where("id={$uid}")->save(array($item=>array('exp', "{$item}+{$num}")));
    }
}