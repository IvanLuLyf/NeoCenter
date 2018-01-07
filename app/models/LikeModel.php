<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 22:20
 */
class LikeModel extends Model
{
    protected $table = 'tp_like';

    public function isLike($uid, $aid, $tid)
    {
        if ($this->where(["uid = ? and tid = ? and aid = ?"], [$uid, $tid, $aid])->fetch()) {
            return 1;
        }
        return 0;
    }

    public function like($uid, $aid, $tid)
    {
        $datas = array(
            'uid' => $uid,
            'tid' => $tid,
            'aid' => $aid,
            'state' => 1
        );
        return $this->add($datas);
    }

    public function unlike($uid, $aid, $tid)
    {
        return $this->where(["uid = ? and tid = ? and aid = ?"], [$uid, $tid, $aid])->delete();
    }
}