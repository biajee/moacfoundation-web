<?php
namespace Common\Model;
use Think\Model;
class VoteModel extends Model {
    protected $tableName = 'Vote';
    public function deleteOne($id) {
        $this->delete($id);
        D('Voteitem')->deleteByVote($id);
        D('Votelog')->deleteByVote($id);
    }
}