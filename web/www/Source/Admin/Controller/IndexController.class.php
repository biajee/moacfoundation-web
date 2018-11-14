<?php
namespace Admin\Controller;
class IndexController extends BaseController {
    public function index() {
        $menu = load_config(MODULE_PATH.'Conf/menu.php');
        $rules = $this->admin['rules'];
        foreach($menu as $k=>$p) {
            if ($rules=='*')
                $menu[$k]['access'] = true;
            foreach($p['submenu'] as $k2=>$v) {
                $arr = explode('/', $v['link']);
                $code = $arr[0].'/view';
                $code2 = $arr[0].'/*';
                if (!empty($v['all']) || $rules=='*' || in_array($code,$rules) || in_array($code2, $rules)) {
                    $menu[$k]['access'] = true;
                    $menu[$k]['submenu'][$k2]['access'] = true;
                }
            }
        }
        $this->assign('menu', $menu);
        $admin = session('admin');
        $this->assign('admininfo', $admin);
		/*统计*/
        $this->display();
    }
	
	public function stats() {
		$stats = array();
		$where = array('status'=>array('eq',0));
		$stats['freecall'] = M('Freecall')->where($where)->count();
		$stats['tourapply'] = M('Tourapply')->where($where)->count();
		$stats['tourwant'] = M('Tourwant')->where($where)->count();
		$this->ajaxReturn($stats);
	}
}