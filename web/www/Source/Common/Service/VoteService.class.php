<?php
namespace Common\Service;
class VoteService {
    public function vote($data) {
        $id = $data['id'];
        $item = $data['item'];
        $voter = $data['voter'];
        //防止频繁提交
        $where = array(
            'voteid' => $id,
            'voter' => $voter
        );
        $cnt = M('Votelog')->where($where)->count();
        if ($cnt>0)
            E('您已投过票，请勿重复提交');

        $data = array();
        $data['id'] = $id;
        $data['votenum'] = array('exp', 'votenum+1');
        M('Vote')->save($data);
        $item = array(
            'id' => $item,
            'votenum' => array('exp', 'votenum+1')
        );
        M('Voteitem')->save($item);
        $log = array(
            'voteid' => $id,
            'voter' => $voter,
            'addtime' => time()
        );
        M('Votelog')->add($log);
    }
}