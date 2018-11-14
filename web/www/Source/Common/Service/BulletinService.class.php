<?php
namespace Common\Service;
class BulletinService extends BaseService {
    /**
     * 添加文章
     * @return int 文章id
     */
    public function addBulletin() {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        $id = M('AppBulletin')->add($data);
        //更新附件信息
        $key = 'Bulletin/'.$id;
        $this->bindAttach($key);
        return $id;
    }

    /**
     * @param $data 保存的数据
     * @return int 影响条数
     */
    public function updateBulletin($data) {
        if (empty($data['id'])) {
            E('信息不存在');
        }
        $data['updatatime'] = time();
        $result = M('AppBulletin')->save($data);
        $key = 'Bulletin/'.$data['id'];
        $this->bindAttach($key);
        return $result;
    }

	public function getBulletins($where, $limit='10', $order='id DESC', $fields='') {
        if (empty($fields))
			$fields = 'id,title,addtime';
        $data['updatetime'] = time();
		$list = M('AppBulletin')->field($fields)
                            ->where($where)
                            ->order($order)
                            ->limit($limit)
                            ->select();
        if (!empty($list)) {
            for($k=0; $k < count($list); $k++) {
                $this->formatData($list[$k]);
            }                   
        } 
        return $list;
	}
    public function getBulletinCount($where) {
        return M('AppBulletin')->where($where)->count();
    }

	public function getBulletin($id, $fields='*') {
		$data = M('AppBulletin')->field($fields)->find($id);
		if (!empty($data)) {
			$data['viewnum'] = $this->updateStat($id, 'viewnum', mt_rand(1,10));
			$this->formatData($data);
		}
		
        return $data;
	}
	
	public function updateStat($id, $item='viewnum', $count=1) {
        $model = M('AppBulletin');
        $data = array('id'=>$id,
                $item => array('exp',"{$item}+{$count}")
            );
        $model->save($data);
		$num = $model->where(array('id'=>array('eq', $id)))->getField($item);
        return $num;
    }
    
    protected function formatData(&$data) {
        if (!empty($data['image'])) {
            $data['image'] = fix_imgurl($data['image']);
        }
		if (!empty($data['content'])) {
			$data['content'] = fix_html($data['content']);
		}
    }
}