<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/8
 * Time: 13:29
 */
class TauthCodeModel extends Model
{
    protected $table = 'tp_tauthcode';

    public function getCode($appKey, $appId, $uid, $timeline)
    {
        $code = md5($uid . $appKey . $timeline);
        $datas = array('uid' => $uid, 'appid' => $appId, 'code' => $code, 'expire' => ($timeline + 604800));
        $this->add($datas);
        return $code;
    }

    public function checkCode($appId, $appCode)
    {
        if ($row = $this->where(['appid= ? and code= ? and UNIX_TIMESTAMP()-expire < 0'], [$appId, $appCode])->fetch()) {
            $this->where(['appid= ? and code= ? and UNIX_TIMESTAMP()-expire < 0'], [$appId, $appCode])->delete();
            return $row['uid'];
        } else {
            return null;
        }
    }
}