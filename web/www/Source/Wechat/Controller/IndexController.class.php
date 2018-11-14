<?php
namespace Wechat\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index() {
        $this->show("微信测试");
    }
}