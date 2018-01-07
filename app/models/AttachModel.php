<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/2
 * Time: 17:46
 */
class AttachModel extends Model
{
    protected $table = 'tp_attach';

    public function upload($uid, $tid, $url)
    {
        $datas = array(
            'uid' => $uid,
            'tid' => $tid,
            'url' => $url
        );
        $this->add($datas);
    }

    public function getAttachByTid($tid)
    {
        if ($row = $this->where(["tid = ?"], [$tid])->fetchAll('url')) {
            return $row;
        } else {
            return null;
        }
    }
}