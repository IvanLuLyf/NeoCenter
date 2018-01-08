<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 17:07
 */
class TokenModel extends Model
{
    protected $table = 'tp_tauthtoken';

    public function check($appKey, $appToken)
    {
        if ($row = $this->where(["appkey = ? and token = ? and UNIX_TIMESTAMP()-expire < 0"], [$appKey, $appToken])->fetch()) {
            return $row['uid'];
        } else {
            return 0;
        }
    }

    public function token($uid, $appKey)
    {
        $timeline = time();
        if ($tokenRow = $this->where(["appkey = :appkey and uid = :uid"], [':appkey' => $appKey, ':uid' => $uid])->fetch()) {
            if ($timeline < intval($tokenRow['expire'])) {
                $token = $tokenRow['token'];
                $expire = $tokenRow['expire'];
            } else {
                $tokenid = $tokenRow['id'];
                $token = md5($uid + $appKey + $timeline);
                $expire = $timeline + 604800;
                $updates = array('token' => $token, 'expire' => $expire);
                $this->where(["id = :id"], [':id' => $tokenid])->update($updates);
            }
        } else {
            $token = md5($uid + $appKey + $timeline);
            $expire = $timeline + 604800;
            $datas = array('uid' => $uid, 'appkey' => $appKey, 'token' => $token, 'expire' => $expire);
            $this->add($datas);
        }
        $response = array(
            'token' => $token,
            'expire' => $expire
        );
        return $response;
    }
}