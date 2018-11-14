<?php
namespace Home\Widget;
use Think\Controller;
class ArchiveWidget extends Controller {
    public function article($group='news', $limit=8, $titlelen=16, $ishot=false, $style='') {
        $arctypeSvc = D('Arctype','Service');
        $type = $arctypeSvc->getType($group);
        $catcode = $type['code'];
        $ids = $arctypeSvc->getLeaves($type['id'], true);
        $where = '1=1';
        $orderby = 'id DESC';
        if ($ids)
            $where .= ' AND catid in(' . $ids . ')';
        if ($ishot)
            $orderby = 'viewnum DESC';
        $list = M('Article')->field('id,title,image,memo,addtime')
            ->where($where)
            ->order($orderby)
            ->limit($limit)
            ->select();
        foreach($list as $k=>$v) {
			$list[$k]['fulltitle'] = $v['title'];
			$list[$k]['title'] = msubstr($v['title'], 0 , $titlelen);
            $list[$k]['url'] = U('News/detail/'.$v['id']);
        }
        $this->assign('ishot', $ishot);
        $this->assign('list', $list);
        $tpl = 'Widget/Archive/article';
        if (!empty($style))
            $tpl .= '_'.$style;
        return $this->fetch($tpl);
    }

    
}