<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/2
 * Time: 1:56
 */
class FriendModel extends Model
{
    protected $table = 'tp_friend';

    public function listFriend($uid, $state = 2)
    {
        return $this->where(["uid = ? and state = ?"], [$uid, $state])->order(["CONVERT( notename USING gbk ) COLLATE gbk_chinese_ci"])->fetchAll();
    }

    public function noteFriend($uid, $username, $notename)
    {
        if ($friend = $this->where(["uid = ? and username = ? and state = 2"], [$uid, $username])->fetch()) {
            $updates = array('notename' => $notename);
            if ($this->where(["uid = :uid and username = :username"], [':uid' => $uid, ':username' => $username])->update($updates)) {
                $response = array(
                    'ret' => 0,
                    'status' => 'ok'
                );
            } else {
                $response = array(
                    'ret' => 1006,
                    'status' => "database error"
                );
            }
        } else {
            $response = array(
                'ret' => 4001,
                'status' => "no friend"
            );
        }
        return $response;
    }
}