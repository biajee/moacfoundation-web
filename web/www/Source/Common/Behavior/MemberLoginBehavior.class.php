<?php
namespace Common\Behavior;
class MemberLoginBehavior extends \Think\Behavior {
    public function run(&$params)
    {
        $filename = RUNTIME_PATH.'Logs/behavior.txt';
        file_put_contents($filename, var_export($params,true));
    }

}