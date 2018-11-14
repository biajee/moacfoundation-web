<?php
namespace Admin\Controller;
class HomeController extends BaseController {
    public function index() {
        $ip = $_SERVER["SERVER_ADDR"];
        if (empty($ip))
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        $host = empty($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
        $data = array(
            'os'=> PHP_OS,
            'server' => $_SERVER['SERVER_SOFTWARE'],
            'host'   => $host . '(' . $ip . ')',
        );
        $this->assign('caption','管理首页');
        $this->assign('data', $data);
        $this->display();
    }
}