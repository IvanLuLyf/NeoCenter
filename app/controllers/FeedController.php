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
                    $imageCount = 0;
                    if (isset($_FILES['images'])) {
                        $paths = $_FILES['images']["tmp_name"];
                        if (is_array($paths)) {
                            $imageCount = count($paths);
                        } else {
                            $imageCount = 1;
                        }
                    }
                    $feedid = (new FeedModel())->sendFeed($message, $api['name'], $api['uid'], $user['username'], $user['nickname'], time(), $imageCount);
                    $attachModel = new AttachModel();
                    if ($imageCount > 0) {
                        for ($i = 0; $i < $imageCount; $i++) {
                            if ((($_FILES["images"]["type"][$i] == "image/gif") || ($_FILES["images"]["type"][$i] == "image/jpeg") || ($_FILES["images"]["type"][$i] == "image/pjpeg"))
                                && ($_FILES["images"]["size"][$i] < 2000000)
                            ) {
                                $t = time() % 1000;
                                $filename = "feed/$feedid-$i-$t.jpg";
                                $this->storage()->upload($filename, $_FILES["images"]["tmp_name"][$i]);
                                $url = $this->storage()->geturl($filename);
                                $attachModel->upload($api['uid'], $feedid, $url);
                            }
                        }
                    }
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
                    foreach ($feeds as &$feed) {
                        if ($feed['image'] != null && $feed['image'] > 0) {
                            $feed['images'] = (new AttachModel())->getAttachByTid($feed['tid']);
                        }
                    }
                    $this->assign('noticnt', (new NotificationModel())->getUnreadCnt($api['uid'])['noticnt']);
                    $this->assign('feeds', $feeds);
                } else {
                    $feed = (new FeedModel())->getFeed($tid);
                    if ($feed['image'] != null && $feed['image'] > 0) {
                        $feed['images'] = (new AttachModel())->getAttachByTid($feed['tid']);
                    }
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

    function ac_like()
    {
        $tid = isset($_REQUEST['tid']) ? $_REQUEST['tid'] : 0;
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['canFeed']);
            if ($api['ret'] == 0) {
                $feedModel = new FeedModel();
                if ($feed = $feedModel->getFeed($tid)) {
                    if ((new LikeModel())->isLike($api['uid'], 3, $tid) == 1) {
                        $this->assign('ret', 3002);
                        $this->assign('status', 'already liked');
                    } else {
                        (new LikeModel())->like($api['uid'], 3, $tid);
                        $like_num = $feedModel->likeFeed($tid);
                        (new NotificationModel())->notify(3, $tid, $feed['uid'], $api['uid'], 'like', '', time());
                        $this->assign('ret', 0);
                        $this->assign('status', 'ok');
                        $this->assign('like_num', $like_num);
                    }
                } else {
                    $this->assign('ret', 3001);
                    $this->assign('status', 'invalid tid');
                }
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }
}