<?php
namespace Common\Service;
class ResoldService extends BaseService {
    protected $rankTypes = null;

    /**
     * 预处理数据
     * @param $data
     */
    protected function prepareData(&$data) {
        $data['tags'] = str_forsearch($data['tags']);
        //处理布局
        $layout = '';
        if (!empty($data['bedroom']))
            $layout .= $data['bedroom'] . '室';
        if (!empty($data['hall']))
            $layout .= $data['hall'] . '厅';
        if (!empty($data['kitchen']))
            $layout .= $data['kitchen'] . '厨';
        if (!empty($data['bathroom']))
            $layout .= $data['bathroom'] . '卫';
        $data['layout'] = $layout;
        if ($data['amount'] && $data['grossarea'])
            $data['price'] = floor($data['amount']*10000/$data['grossarea']);
        if (!empty($data['builttime'])) {
            $year = date('Y', time());
            $data['builtage'] = $year - $data['builttime'];
        }
        //附加数据
        $data['Addon'] = array(
            'images' => $data['images'],
            'album' => $data['album'],
            'content' => $data['content']
        );
        unset($data['images']);
        unset($data['album']);
        unset($data['content']);
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        $data['updatetime'] = time();
    }
    /**
     * 添加信息
     * @param $data 数据
     * @return int id
     */
    public function addResold($data) {
        $this->prepareData($data);
        $id = D('Resold')->relation('Addon')->add($data);
        //更新附件信息
        $key = 'Resold/'.$id;
        $this->bindAttach($key);
        return $id;
    }

    /**
     * @param $data 保存的数据
     * @return int 影响条数
     */
    public function updateResold($data) {
        if (empty($data['id'])) {
            E('信息不存在');
        }
        $this->prepareData($data);
        $result = D('Resold')->relation('Addon')->save($data);
        $key = 'Resold/'.$data['id'];
        $this->bindAttach($key);
        return $result;
    }
    //获得数量
    public function getResoldCount($where) {
        return M('Resold')->where($where)->count();
    }

	public function getResolds($where, $limit='10', $order='id desc') {
        $fields = '*';
		$list = M('Resold')->field($fields)
                            ->where($where)
                            ->order($order)
                            ->limit($limit)
                            ->select();
		foreach($list as $k=>$v){
			$list[$k]['tagarr'] = explode(' ', trim($list[$k]['feature'], ' '));
		}
        return $list;
	}
	
	public function getResold($id) {
		$data = D('Resold')->relation(true)->find($id);
		return $data;
	
	}
	
	public function updateStat($id, $item) {
        $model = M('Resold');
        $data = array('id'=>$id,
                $item => array('exp',"{$item}+1")
            );
        $model->save($data);
		$num = $model->where(array('id'=>array('eq', $id)))->getField($item);
        return $num;
    }

    public function getRankTypes() {
        if (empty($this->_rankTypes)) {
            $cacheSvc = D('Cache','Service');
            $this->_rankTypes = $cacheSvc->getData('ResoldRankMap');
        }
        return $this->_rankTypes;
    }

    public function getRankType($code) {
        $this->getRankTypes();
        foreach ($this->_rankTypes as $k=>$v) {
            if ($v['code'] == $code)
                return $v;
        }
    }

    public function delResold($id) {
        M('Resold')->delete($id);
        $key = 'Resold/'.$id;
        $this->delAttach($key);
    }

}