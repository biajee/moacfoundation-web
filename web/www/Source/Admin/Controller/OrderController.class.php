<?php
namespace Admin\Controller;
class OrderController extends BaseController {
    public function index() {
        $model = D('Order');
        $where = '1=1';
        $status = I('status');
        $username = I('username');
        $ordersn = I('ordersn');
        $orderstatus = $this->cacheSvc->getData('OrderStatusMap');
        if (!empty($status)) {
            $where .= " AND status= '$status'";
        }
        if (!empty($username)) {
            $where .= " AND username ='{$username}'";
        }
        if (!empty($ordersn))
            $where .= " AND ordersn LIKE '%{$ordersn}%'"; 
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        foreach($list as $k=>$v) {
            $list[$k]['status'] = $orderstatus[$v['status']];
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->assign('orderstatus', $orderstatus);
        $this->assign('caption','管理订单');
        $this->display();
    }

    public function edit() {
        $id = I('id');
        $data = M('Order')->find($id);
        $data['user'] = M('Member')->find($data['uid']);
        $orderstatus = $this->cacheSvc->getData('OrderStatusMap');
        $supplier = $this->cacheSvc->getData('SupplierMlt');
        $detail = M('Orderdetail')->where("orderid=$id")->select();
        foreach($detail as $k=>$v) {
            $detail[$k]['supplier'] = $supplier[$v['supid']];
        }
        $this->assign('data', $data);
        $this->assign('detail', $detail);
        $this->assign('orderstatus', $orderstatus);
        $this->assign('caption', '订单详细');
        $this->display();
    }

    public function update() {
        $id = I('id');
        $status = I('status');
        $data['status'] = $status;
        M('Order')->where("id='$id'")->save($data);
        $this->success('保存成功', U('Order/index'));
    }
    public function delete() {
        $id = I('id');
        D('Order')->deleteOne($id);
        $this->success('删除成功', U('Order/index'));
    }
    
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Order');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                D('Order')->deleteBatch($id);
                break;
        }
        $this->success('执行成功',U('Order/index'));
    }
}