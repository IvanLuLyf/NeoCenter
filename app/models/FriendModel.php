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

    public function addFriend($uid, $fuid, $username, $fusername, $nickname, $fnickname)
    {
        if ($username != $fusername) {
            if ($this->where(["uid = :uid and username = :username"], [':uid' => $uid, ':username' => $fusername])->fetch()) {
                $response = array(
                    'ret' => 1009,
                    'status' => "already exist"
                );
            } else {
                $datas = array('uid' => $uid, 'fuid' => $fuid, 'username' => $fusername, 'notename' => $fnickname, 'state' => 0);
                $this->add($datas);
                $datas = array('uid' => $fuid, 'fuid' => $uid, 'username' => $username, 'notename' => $nickname, 'state' => 1);
                $this->add($datas);
                $response = array(
                    'ret' => 0,
                    'status' => "ok"
                );
            }
        } else {
            $response = array(
                'ret' => 1005,
                'status' => "invalid username"
            );
        }
        return $response;
    }

    public function acceptFriend($uid, $fuid, $username, $fusername)
    {
        if ($row = $this->where(["uid = :uid and username = :username and state = 1"], [':uid' => $uid, ':username' => $fusername])->fetch()) {
            $updates = array('state' => 2);
            $this->where(["uid = :uid and username= :username"], [':uid' => $uid, ':username' => $fusername])->update($updates);
            $this->where(["uid = :uid and username= :username"], [':uid' => $fuid, ':username' => $username])->update($updates);
            $response = array(
                'ret' => 0,
                'status' => "ok"
            );
        } else {
            $response = array(
                'ret' => 1005,
                'status' => "invalid username"
            );
        }
        return $response;
    }
}