<?php
namespace Home\Widget;
use Think\Controller;
class BlockWidget extends Controller
{
    public function custom($code)
    {
        $model = M('Block');
        $data = $model->where("code='$code'")->getField('content');
        $data = html_entity_decode($data);
        return $data;
    }
}