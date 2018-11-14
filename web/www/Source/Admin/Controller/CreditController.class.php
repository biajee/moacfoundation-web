<?php
namespace Admin\Controller;
class CreditController extends BaseController {
    public function index() {
        $model = D('Credit');
        $where = array();
        $uid = I('uid');
        if ($uid) {
            $map['buyer'] = array('eq', $uid);
            $map['seller'] = array('eq', $uid);
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        $username = I('username');
        if ($username) {
            if (valid_mobile($username))
                $where2['mobile'] = array('eq', $username);
            elseif (valid_email($username))
                $where2['email'] = array('eq', $username);
            else {
                $where2['username'] = array('eq', $username);
                $where2['nickname'] = array('eq', $username);
                $where2['_logic'] = 'or';
            }
            $uids = M('Member')->where($where2)->getField('id', true);
            if ($uids) {
                $map['buyerid'] = array('in', $uids);
                $map['sellerid'] = array('in', $uids);
                $map['_logic'] = 'or';
                $where['_complex'] = $map;
            } else {
                $where['buyerid'] = 0;
            }
        }
        $count = $model->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
            ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('caption','管理服务');
        $this->display();
    }
    
    public function detail() {
        $id = I('id');
        $model = D('Credit');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->assign('caption', '查看详情');
        $this->display();
    }
    
    public function delete() {
        $id = I('id');
        D('Credit')->deleteOne($id);
        $this->success('删除成功', U('Credit/index'));
    }

    public function batch() {
        $ids = $_POST['id'];
        if (empty($ids))
            $this->error('请选择项目');
        $model = service('Daybook');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                foreach($ids as $id) {
                    $model->delDaybook($id);
                }
                break;
        }
        $this->success('执行成功',U('Daybook/index'));
    }
}