<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/2
 * Time: 11:05
 */
class AvatarModel extends Model
{
    protected $table = 'tp_avatar';

    public function upload($uid, $path)
    {
        if ($this->where(["uid = ?"], [$uid])->fetch()) {
            $updates = array('url' => $path);
            return $this->where(["uid = :uid"], [':uid' => $uid])->update($updates);
        } else {
            $datas = array('uid' => $uid, 'url' => $path);
            return $this->add($datas);
        }
    }

    public function getAvatar($uid)
    {
        if ($row = $this->where(["uid = ?"], [$uid])->fetch()) {
            return $row['url'];
        } else {
            return '/static/images/avatar.jpg';
        }
    }
}