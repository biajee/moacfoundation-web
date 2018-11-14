<?php
namespace Common\Service;
class ApplyService extends BaseService {
    /**
     * 自动构建对象
     * @return mixed
     */
    public function buildInfo() {
        return M('Info')->create();
    }
    /**
     * 添加文章
     * @return int 文章id
     */
    public function addInfo($data) {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        $id = M('Info')->add($data);
        //更新附件信息
        $key = 'Info/'.$id;
        $this->bindAttach($key);
        //更新统计
        $uid = $data['memid'];
        service('Member')->updateStat($uid, 'infonum');
        return $id;
    }

    /**
     * @param $data 保存的数据
     * @return int 影响条数
     */
    public function updateInfo($data) {
        if (empty($data['id'])) {
            E('信息不存在');
        }
        $data['updatatime'] = time();
        $result = M('Info')->save($data);
        $key = 'Info/'.$data['id'];
        $this->bindAttach($key);
        return $result;
    }
	//获取报名表列表
	public function getApplys($where, $limit='10', $order='id DESC', $fields='') {
        if (empty($fields))
			$fields = '*';
		$list = M('Apply')->field($fields)
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
    public function getApplyCount($where) {
        return M('Apply')->where($where)->count();
    }
    public function getCartCount($where) {
        return M('Cart')->where($where)->count();
    }
    
	public function getInfo($id, $fields='*') {

		$data = M('Info')->field($fields)->find($id);
		if (!empty($data)) {
			$data['viewnum'] = $this->updateStat($id, 'viewnum', 1);
			$this->formatData($data);
		}
		
        return $data;
	}
	public function delInfo($id) {
        return $this->delInfos(array('id'=>array('eq', $id)));
    }
	public function delInfos($where) {
        return M('Info')->where($where)->delete();
    }

    public function updateStatus($where, $status) {
        $ret = M('Info')->where($where)->save(array('status'=>$status));
        return $ret;
    }

	public function updateStat($id, $item='viewnum', $count=1) {
        $model = M('Info');
        $data = array('id'=>$id,
                $item => array('exp',"{$item}+{$count}")
            );
        $model->save($data);
		$num = $model->where(array('id'=>array('eq', $id)))->getField($item);
        return $num;
    }

    protected function formatData(&$data) {
        static $district = null;
        static $realm = null;
        static $module = null;
        $cache = service('Cache');
        if (empty($district)) {
            $district = $cache->getData('DistrictMlt');
        }
        if (empty($realm)) {
            $realm = $cache->getData('RealmMlt');
        }
        if (empty($module)) {
            $module = $cache->getData('InfoModuleMlt');
        }
        if ($data['images']) {
            $data['imagelist'] = explode(',', $data['images']);
        }
        $data['countrystr'] = $district[$data['country']]['title'];
        $data['citystr'] = $district[$data['city']]['title'];
        $data['realmstr'] = $realm[$data['realm']]['title'];
        $data['realm2str'] = $realm[$data['realm2']]['title'];
        $data['modulestr'] = $module[$data['module']]['title'];
        $data['reviewnum'] = M('Comment')->where("module='info' AND itemid=$data[id]")->count();
        $data['praisenum'] = M('Praise')->where("module='info' AND itemid=$data[id]")->count();
        /*if ($data['content'] != '') {
            $data['contentfmt'] = nl2br($data['content']);
        }*/
    }
    public function getStats($id) {
        $stat['favnum'] = service('Favorite')->getFavoriteCount("module='info' AND itemid=$id");
        $stat['reviewnum'] = service('Comment')->getCommentCount("module='info' AND itemid=$id");
        return $stat;
    }
    public function getStatsByUser($uid=null) {
        $where = array(
            'module' => array('eq', 'service'),
            'status' => array('lt', 2)
        );
        if ($uid) {
            $where['memid'] = array('eq', $uid);
        }
        $stat['service'] = M('Info')->where($where)->count();
        $where['module'][1] = 'task';
        $stat['task'] = M('Info')->where($where)->count();
        $where['module'][1] = 'news';
        $stat['news'] = M('Info')->where($where)->count();
        return $stat;
    }
}