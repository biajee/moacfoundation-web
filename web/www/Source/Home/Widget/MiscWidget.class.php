<?php
namespace Home\Widget;
use Think\Controller;
class MiscWidget extends Controller {
    public function contact() {
        $setting = D('Cache','Service')->getData('SettingMap');
        $this->assign('setting', $setting);
        return $this->fetch('Block/contact');
    }      

    public function goods($count, $ishot=false) {
        $where = '1=1';
        if ($ishot)
            $where .= ' AND ishot=1';
        $list = M('Goods')->field('id,title,image,number,addtime')
                                    ->where($where)
                                    ->order('id DESC')
                                    ->limit($count)
                                    ->select();
        foreach($list as $k=>$v) {
            $list[$k]['url'] = U('Supply/detail?id='.$v['id']);
        }
        $this->assign('ishot', $ishot);
        $this->assign('list', $list);
        return $this->fetch('Block/goods');
    }
}