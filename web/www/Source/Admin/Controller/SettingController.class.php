<?php
namespace Admin\Controller;
class SettingController extends BaseController {
    private function edit() {
        $typeModel = M('Settype');
        $types = $typeModel->select();
        $type = I('type');
        if (empty($type))
            $type="base";
        $model = M('Setting');
        $list = $model->where("catid='$type'")->order('sortno')->select();
        $this->assign('type', $type);
        $this->assign('types', $types);
        $this->assign('list', $list);
        $this->display('index');
    }
    public function _empty($name) {
        if (empty($name) || $name=='index')
            $name = 'base';
        $this->edit($name);
    }
    public function update() {
        $type = $_POST['type'];
        $model = M('setting');
        $list = $model->where("catid='$type'")->getField('var', true);
        foreach($list as $v) {
            $val = trim($_POST[$v]);
            if ($val != '') {
                $model->where("var='$v'")->save(array('val'=>$val));
            }
        }
        $this->cache();
        $this->success('保存成功',U('Setting/'.$type));
    }
    private function cache() {
        $this->cacheSvc->cacheData('Setting');
    }
}