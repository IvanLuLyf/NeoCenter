<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/2
 * Time: 1:17
 */
class NotificationModel extends Model
{
    protected $table = 'tp_notification';

    public function getUnreadCnt($uid)
    {
        return $this->where(["uid = ? and is_read=0"], [$uid])->fetch("count(*) as noticnt");
    }

    public function notify($aid, $tid, $toid, $fromid, $action, $message, $timeline)
    {
        $datas = array(
            'aid' => $aid,
            'uid' => $toid,
            'tid' => $tid,
            'from_uid' => $fromid,
            'action' => $action,
            'message' => $message,
            'timeline' => $timeline
        );
        return $this->add($datas);
    }
}