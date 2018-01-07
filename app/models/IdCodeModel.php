<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/7
 * Time: 14:52
 */
class IdCodeModel extends Model
{
    protected $table = 'tp_idcode';

    public function getIdCode($uid)
    {
        if ($row = $this->where(['uid = :uid'], [':uid' => $uid])->fetch()) {
            return $row['idcode'];
        } else {
            require_once APP_PATH . "library/IdCode.php";
            $codes = getIdCodes($uid);
            for ($i = 0; $i < 4; $i++) {
                $code = $codes[$i];
                if ($row = $this->where(['idcode = :idcode'], [':idcode' => $code])->fetch()) {
                    continue;
                } else {
                    $datas = array('uid' => $uid, 'idcode' => $code);
                    $this->add($datas);
                    return $code;
                }
            }
        }
    }

    public function getUidByIdCode($idcode)
    {
        if ($idrow = $this->where(['idcode = :idcode'], [':idcode' => $idcode])->fetch()) {
            return $idrow['uid'];
        } else {
            return 0;
        }
    }
}