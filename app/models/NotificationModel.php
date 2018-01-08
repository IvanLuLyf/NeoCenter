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

    public function getNotice($uid)
    {
        $notices = $this->join('tp_friend', ["tp_notification.from_uid=tp_friend.fuid AND tp_friend.uid=$uid AND tp_friend.state=2"], 'LEFT')
            ->where(["tp_notification.uid=? AND tp_notification.uid!=tp_notification.from_uid AND tp_notification.is_read=0"], [$uid])
            ->order(["nid desc"])
            ->fetchAll('tp_notification.*,tp_friend.notename');
        $this->where(["uid = :uid and is_read=0"], [':uid' => $uid])->update(['is_read' => 1]);
        return $notices;
    }

    public function notify($aid, $tid, $toid, $fromid, $action, $message, $timeline)
    {
        if ($toid != $fromid) {
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
}