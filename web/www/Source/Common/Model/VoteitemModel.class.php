<?php
namespace Common\Model;
use Think\Model;
class VoteitemModel extends Model {
    protected $tableName = 'Voteitem';
    public function deleteOne($id) {
        $this->delete($id);
    }
    public function deleteByVote($voteid) {
        $this->where("voteid=$voteid")->delete();
    }
}