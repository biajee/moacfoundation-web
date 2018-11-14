<?php
namespace Admin\Controller;
class FeedbackController extends BaseController {
    public function index() {
        $list = M('Feedback')->order('id DESC')->select();
        $this->assign('caption', '留言反馈');
        $this->assign('list', $list);
        $this->display();
    }

    public function edit() {
        $id = I('id');
        $data = M('Feedback')->find($id);
        $this->assign('caption','回复留言');
        $this->assign('data', $data);
        $this->display();
    }
    //更新
    public function update() {
        $id = I('id');
        $recontent = I('recontent');
        if (!empty($recontent)) {
            $data = array('id'=>$id, 'recontent'=>$recontent, 'retime'=>time(), 'status'=>1);
            M('Feedback')->save($data);
            $this->show('<script>parent.location.reload();</script>');
        }
    }
    //删除
    public function delete() {
        $id = I('id');
        D('Feedback')->delete($id);
        $this->success('删除成功', U('Feedback/index'));
    }
    //批量操作
    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Feedback');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'delete':
                $model->where("id IN($ids)")->delete();
                break;
            case 'show':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'hide':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Feedback/index'));
    }
}
