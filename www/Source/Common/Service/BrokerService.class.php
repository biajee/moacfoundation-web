<?php
namespace Common\Service;
/**
 * Class BrokerService 会员服务
 * @package Common\Service
 */
class BrokerService extends MemberService{
    /**
     * 获取会员信息
     * @param $key id(必须为整数)或用户名
     * @return mixed
     */
    public function getBroker($key) {
        if (is_numeric($key))
            $where['id'] = array('eq', $key);
        else
            $where['username'] = array('eq', $key);
        $data = D('Broker')->relation('Member')->where($where)->find();
        return $data;
    }

    /**
     * 获取会员列表
     * @param $where 条件
     * @param $order 排序
     * @param $limit 条数
     * @return mixed 会员列表
     */
    public function getBrokers($where, $order, $limit) {
        return M('Broker')->where($where)
                            ->order($order)
                            ->limit($limit);
    }
	public function addBroker($data) {
		$data['addtime'] = time();
		M('Broker')->add($data);
	}
    /**
     * 注册会员
     * @param $data
     * @return int
     */
    public function register($data) {
        //注册
        $mobile = $data['mobile'];
        $password = $data['password'];
        $var = 'mobile';
        $val = $mobile;
        $result = $this->exists($var, $val);
        if (!$result)
            E('手机号已被注册');
        $user = array(
            'username' => $mobile,
            'password' => $mobile,
            'mobile' => $mobile,
            'isbroker' => 1
        );
        $uid = $this->addMember($user);
        $username = $this->genUsername($uid);
        $user = array(
            'id' => $uid,
            'username' => $username,
            'password' => $password,
            'lastlogintime' => time()
        );
        $this->updateMember($user);
       //更新broker
        $broker = array(
            'id' => $uid,
            'username' => $username,
            'surname' => $data['surname'],
            'vocation' => $data['vocation'],
            'attention' => $data['attention'],
            'phone' => $data['mobile'],
            'addtime' => time()
        );
        $result = M('Broker')->add($broker);
        return $uid;
    }

}