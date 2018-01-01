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
}