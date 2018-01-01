<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 22:57
 */
class FeedController extends Controller
{
    public function send()
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
}