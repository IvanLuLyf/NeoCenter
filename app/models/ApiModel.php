<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 16:45
 */
class ApiModel extends Model
{
    protected $table = 'tp_api';

    public function check($appKey)
    {
        if ($row = $this->where(["appkey = ?"], [$appKey])->fetch()) {
            $reponse = array(
                'name' => $row['appname'],
                'type' => $row['type'],
                'canGetInfo' => (intval($row['auth']) & 1) && true,
                'canFeed' => (intval($row['auth']) & 2) && true,
                'canGetFriend' => (intval($row['auth']) & 4) && true
            );
            return $reponse;
        } else {
            return null;
        }
    }

    public function validate($appKey, $appSecret)
    {
        if ($row = $this->where(["appkey = ? and appsecret = ?"], [$appKey, $appSecret])->fetch()) {
            $reponse = array(
                'name' => $row['appname'],
                'type' => $row['type'],
                'canGetInfo' => (intval($row['auth']) & 1) && true,
                'canFeed' => (intval($row['auth']) & 2) && true,
                'canGetFriend' => (intval($row['auth']) & 4) && true
            );
            return $reponse;
        } else {
            return null;
        }
    }
}