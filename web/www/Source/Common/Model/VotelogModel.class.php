<?php
namespace Common\Model;
use Think\Model;
class VotelogModel extends Model {
    protected $tableName = 'Votelog';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteByVote($voteid) {
        $this->where("voteid=$voteid")->delete();
    }
}