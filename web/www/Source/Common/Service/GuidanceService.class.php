<?php
namespace Common\Service;
class GuidanceService extends BaseService {
    /**
     * 添加文章
     * @return int 文章id
     */
    public function addGuidance() {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        $id = M('AppGuidance')->add($data);
        //更新附件信息
        $key = 'Guidance/'.$id;
        $this->bindAttach($key);
        return $id;
    }

    /**
     * @param $data 保存的数据
     * @return int 影响条数
     */
    public function updateGuidance($data) {
        if (empty($data['id'])) {
            E('信息不存在');
        }
        $data['updatatime'] = time();
        $result = M('AppGuidance')->save($data);
        $key = 'Guidance/'.$data['id'];
        $this->bindAttach($key);
        return $result;
    }

	public function getGuidances($where, $limit='10', $order='id DESC', $fields='') {
        if (empty($fields))
			$fields = 'id,title,image,link,linkparam,memo,addtime';
        $data['updatetime'] = time();
		$list = M('AppGuidance')->field($fields)
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
    public function getGuidanceCount($where) {
        return M('AppGuidance')->where($where)->count();
    }

	public function getGuidance($where, $fields='*') {
		if (!is_array($where)) {
			$where = array('id' => array('eq', $where));
		}
		$data = M('AppGuidance')->where($where)->field($fields)->find();
		if (!empty($data)) {
		    $num = mt_rand(1,10);
			$data['viewnum'] = $this->updateStat($id, 'viewnum', $num);
			$this->formatData($data);
		}
		
        return $data;
	}
	
	public function updateStat($id, $item, $num=1) {
        $model = M('AppGuidance');
        $data = array('id'=>$id,
                $item => array('exp',"{$item}+{$num}")
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