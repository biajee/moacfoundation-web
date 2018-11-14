<?php
namespace Service\Controller;
use Think\Controller;

class MapController extends Controller
{
    public function index() {
        $lng = I('lng');
        $lat = I('lat');
        $zoom = I('zoom');
        if (!empty($lng)) {
            $data = array(
                'lng' => $lng,
                'lat' => $lat,
                'zoom' => $zoom
            );
            $this->assign('data', $data);
        }
        $this->display();
    }
}