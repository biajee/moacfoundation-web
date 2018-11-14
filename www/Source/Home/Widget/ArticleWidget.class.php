<?php
namespace Home\Widget;
use Think\Controller;
class ArticleWidget extends Controller {
    public function article($group='news', $title='最新动态', $count=8, $titlelen=16, $ishot=false, $style='') {
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
            ->limit($count)
            ->select();
        foreach($list as $k=>$v) {
			$list[$k]['fulltitle'] = $v['title'];
			$list[$k]['title'] = msubstr($v['title'], 0 , $titlelen);
            $list[$k]['url'] = U(ucfirst($catcode).'/detail/'.$v['id']);
        }
        $this->assign('title', $title);
        $this->assign('ishot', $ishot);
        $this->assign('list', $list);
        $tpl = 'article';
        if (!empty($style))
            $tpl .= '_'.$style;
        $this->display('Block/'.$tpl);
    }

    
}