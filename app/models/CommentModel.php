<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 22:00
 */
class CommentModel extends Model
{
    protected $table = 'tp_comments';

    public function listComment($aid, $tid, $page = 1)
    {
        return $this->where(["aid = ? and tid = ?"], [$aid, $tid])
            ->limit(20, ($page - 1) * 20)
            ->fetchAll();
    }

    public function sendComment($aid, $tid, $username, $nickname, $message, $timeline)
    {
        $datas = array(
            'tid' => $tid,
            'username' => $username,
            'nickname' => $nickname,
            'message' => $message,
            'timeline' => $timeline,
            'aid' => $aid
        );
        return $this->add($datas);
    }
}