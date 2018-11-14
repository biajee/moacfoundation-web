<?php
namespace Common\Service;
class ParityService {
   public function getItemsCount($where) {
       $model = M('AppParityitem');
       return $model->where($where)->count();
   }

   public function getItemsCountById($id) {
       $where = array('parityid'=>array('eq', $id),
           'isparity'=>array('gt',0)
		   );
       return $this->getItemsCount($where);
   }
    public function getItemsCountByDealerId($id) {
        $where = array('dealerid'=>array('eq', $id),
           'isparity'=>array('gt',0)
		   );
        return $this->getItemsCount($where);
    }
   public function getItemsCountByDealerName($name) {
       $where = array('dealername'=>array('eq', $name),
           'isparity'=>array('gt',0));
       return $this->getItemsCount($where);
   }

   public function getParity($id) {
       $model = M('AppParity');
       $addnum = mt_rand(1, 10);
       $model->save(array('id'=>$id, 'viewnum'=>array('exp',"viewnum+{$addnum}")));
       $parity = $model->find($id);
       $proModel = service('Building');
       $project = $proModel->getBuilding($id,'price,unit,image,viewnum', false);
       $parity = array_merge($parity, $project);
	   $where = array(
		'parityid'=>array('eq', $id)
		//'isparity' => array('gt', 0)
	   );
       $parity['items'] = $this->getParityItems($where);
       return $parity;
   }

   public function getParities($where, $limit, $order) {
       $model = M('AppParity');
       $plist = $model->where($where)->order($order)->limit($limit)->select();
        return $plist;
   }
    public function getParitiesByDealer($where, $limit) {
        $model = M('AppParityitem');
        if (empty($order))
            $order = 'sortno ASC,id DESC';
        $model->where($where)
            ->order($order);
        if ($limit)
            $model ->limit($limit);
        $parList = $model->select();
        $proModel = service('Building');
        foreach($parList as $k=>$v) {
            $project = $proModel->getBuilding($v['parityid'],'id,title,image,price,unit', false);
            $project['benefit'] = $v['benefit'];
            $list[] = $project;
        }
        return $list;
    }
   public function getParityItems($where, $limit='', $order='') {
       if (!is_array($where)) {
           $where = array('parityid'=>array('eq', $where));
       }
       $model = M('AppParityitem');
       if (empty($order))
         $order = 'sortno DESC,id DESC';
       $model->where($where)->order($order);

       if ($limit)
           $model ->limit($limit);
       $items = $model->select();
       $deaModel = service('Dealer');
       foreach($items as $k=>$v) {
           $dealer = $deaModel->getDealer($v['dealerid'], 'id,title,subtitle,image,link as dlink,phone');
           $dealer['benefit'] = $v['benefit'];
		   $dealer['link'] = $v['link'];
           $list[] = $dealer;
       }
       return $list;
   }

   public function trigerUpdate($id) {
       M('AppParity')->save(array('id'=>$id,'updatetime'=>time()));
   }
}