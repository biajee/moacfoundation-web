<?php
namespace Home\Controller;
use Think\Controller;
class SecretController extends Controller {
   public function _empty() {
       $this->redirect('Error/index');
   }
   public function update() {
       $m = M();
       $m1 = M('Building');
       $m3 = M('Buildingparity');
       $m2 = M('AppParity');
       $m4 = M('AppParityitem');
       $m->execute('truncate table edb_app_parity');
       $m->execute('truncate table edb_app_parityitem');
       $list = $m1->order('id asc')->select();
       foreach($list as $v) {
           $data = array(
               'id' => $v['id'],
               'title' => $v['title'],
               'mainproduct' => $v['mainproduct'],
               'district' => $v['district'],
               'zone' => $v['zone'],
               'addtime' => $v['addtime']
           );
           $m2->add($data);
           $parityid = $v['id'];
           $list2 = $m3->where("buildingid=".$v['id'])->select();
           foreach ($list2 as $v2) {
                $data2  = array(
                    'parityid' => $parityid,
                    'title' => $v2['title'],
                    'link' => $v2['link'],
                    'benefit'=> $v2['price'],
                    'sortno' => $v2['sortno'],
                    'isparity' => 1
                );
               $m4->add($data2);
           }
       }
       $this->show('处理完毕');
   }
}