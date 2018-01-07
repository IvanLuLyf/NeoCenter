<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 20:59
 */
class FeedModel extends Model
{

    protected $table = 'tp_feeds';

    public function listFeed($uid, $page = 1)
    {
        return $this->join('tp_friend', ["tp_feeds.username=tp_friend.username AND tp_friend.uid=$uid AND tp_friend.state=2"], "LEFT")
            ->join('tp_like', ["tp_feeds.tid=tp_like.tid and tp_like.aid = 3 and tp_like.uid=$uid"], "LEFT")
            ->where(["tp_friend.uid = ? OR tp_feeds.uid=?"], [$uid, $uid])
            ->order(["tid desc"])
            ->limit(20, ($page - 1) * 20)
            ->fetchAll("tp_feeds.*,tp_friend.notename,(tp_like.state is not null) as islike");
    }

    public function sendFeed($message, $source, $uid, $username, $nickname, $timeline, $image = 0)
    {
        $datas = array(
            'uid' => $uid,
            'username' => $username,
            'nickname' => $nickname,
            'source' => $source,
            'message' => $message,
            'timeline' => $timeline,
            'image' => $image
        );
        return $this->add($datas);
    }

    public function getFeed($tid)
    {
        return $this->where(["tid = ?"], [$tid])->fetch();
    }

    public function likeFeed($tid)
    {
        $row = $this->where(["tid = :tid"], [':tid' => $tid])->fetch();
        $updates = array('like_num' => intval($row['like_num']) + 1);
        $this->where(["tid = :tid"], [':tid' => $tid])->update($updates);
        $row = $this->where(["tid = :tid"], [':tid' => $tid])->fetch();
        return intval($row['like_num']);
    }
}