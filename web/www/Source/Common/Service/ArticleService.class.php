<?php
namespace Common\Service;
class ArticleService extends BaseService {
    /**
     * 添加文章
     * @return int 文章id
     */
    public function addArticle() {
        if (empty($data['addtime']))
            $data['addtime'] = time();
        $id = M('Article')->add($data);
        //更新附件信息
        $key = 'Article/'.$id;
        $this->bindAttach($key);
        return $id;
    }

    /**
     * @param $data 保存的数据
     * @return int 影响条数
     */
    public function updateArticle($data) {
        if (empty($data['id'])) {
            E('信息不存在');
        }
        $data['updatatime'] = time();
        $result = M('Article')->save($data);
        $key = 'Article/'.$data['id'];
        $this->bindAttach($key);
        return $result;
    }

	public function getArticles($where, $limit='10', $order='id DESC', $fields='') {
        if (empty($fields))
			$fields = 'id,title,image,memo,addtime';
        $data['updatetime'] = time();
		$list = M('Article')->field($fields)
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
    public function getArticleCount($where) {
        return M('Article')->where($where)->count();
    }
    public function getArticlesByType($upid, $limit=10, $order='id DESC', $fields=''){
        $typeSvc = D('Arctype','Service');
        if (!is_numeric($upid)) {
            $type = $typeSvc->getType($upid);
            $upid = $type['id'];
        }
        $ids = $typeSvc->getLeaves($upid);
        $where['catid'] = array('in',$ids);
        return $this->getArticles($where, $limit, $order, $fields);
    }

	public function getArticle($id, $fields='*') {
		$data = M('Article')->field($fields)->find($id);
		if (!empty($data)) {
			$data['viewnum'] = $this->updateStat($id, 'viewnum', mt_rand(1,10));
			$this->formatData($data);
		}
		
        return $data;
	}
	
	public function updateStat($id, $item='viewnum', $count=1) {
        $model = M('Article');
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