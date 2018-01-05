<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 22:57
 */
class FeedController extends Controller
{
    public function ac_send()
    {
        if ($this->_mode == 1) {
            if (isset($_POST['message'])) {
                $api = $this->filter('Api', ['canFeed']);
                if ($api['ret'] == 0) {
                    $message = $_POST['message'];
                    $user = (new UserModel())->getUserByUid($api['uid']);
                    $feedid = (new FeedModel())->sendFeed($message, $api['name'], $api['uid'], $user['username'], $user['nickname'], time());
                    $this->assign('ret', 0);
                    $this->assign('status', 'ok');
                    $this->assign('tid', $feedid);
                } else {
                    $this->assignAll($api);
                }
            } else {
                $this->assign('ret', 1004);
                $this->assign('status', 'empty arguments');
            }
        }
        $this->render();
    }

    public function ac_view($tid = 0, $page = 1)
    {
        if (isset($_REQUEST['tid'])) $tid = $_REQUEST['tid'];
        if (isset($_REQUEST['page'])) $page = $_REQUEST['page'];
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['canFeed']);
            if ($api['ret'] == 0) {
                $this->assign('ret', 0);
                $this->assign('status', 'ok');
                $this->assign('page', $page);
                if ($tid == 0) {
                    $feeds = (new FeedModel())->listFeed($api['uid'], $page);
                    $this->assign('feeds', $feeds);
                } else {
                    $feed = (new FeedModel())->getFeed($tid);
                    $comments = (new CommentModel())->listComment(3, $tid, $page);
                    $this->assign('feed', $feed);
                    $this->assign('comments', $comments);
                }
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }

    public function ac_comment($tid = 0)
    {
        if (isset($_REQUEST['tid'])) $tid = $_REQUEST['tid'];
        if ($this->_mode == 1) {
            if (isset($_POST['message'])) {

                $api = $this->filter('Api', ['canFeed']);
                if ($api['ret'] == 0) {
                    if ($feed = (new FeedModel())->getFeed($tid)) {
                        $message = $_POST['message'];
                        $user = (new UserModel())->getUserByUid($api['uid']);
                        $commentid = (new CommentModel())->sendComment(3, $tid, $user['username'], $user['nickname'], $message, time());
                        (new NotificationModel())->notify(3, $tid, $feed['uid'], $api['uid'], 'comment', $message, time());
                        $this->assign('ret', 0);
                        $this->assign('status', 'ok');
                        $this->assign('tid', $tid);
                        $this->assign('cmid', $commentid);
                    } else {
                        $this->assign('ret', 3001);
                        $this->assign('status', 'invalid tid');
                    }
                } else {
                    $this->assignAll($api);
                }

            } else {
                $this->assign('ret', 1004);
                $this->assign('status', 'empty arguments');
            }
        }
        $this->render();
    }
}