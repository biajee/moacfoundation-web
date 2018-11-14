<?php
namespace Common\Model;
use Think\Model;
class ArticleModel extends Model {
    protected $tableName = 'article';
    public function deleteOne($id) {
        $this->delete($id);
        $key = 'Article/'.$id;
        D('Attachment')->deleteByKey($key);
    }
    public function deleteBatch($idArr) {
        foreach($idArr as $id) {
            $this->deleteOne($id);
        }
    }
}