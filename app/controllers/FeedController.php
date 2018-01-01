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
            if (isset($_POST['appkey']) && isset($_POST['token']) && isset($_POST['message'])) {
                $appKey = $_POST['appkey'];
                $appToken = $_POST['token'];
                $message = $_POST['message'];
                if ($apiInfo = (new ApiModel())->check($appKey)) {
                    if ($apiInfo['type'] == 1 || $apiInfo['canFeed'] == true) {
                        $userId = (new TokenModel())->check($appKey, $appToken);
                        if ($userId != 0) {
                            $user = (new UserModel())->getUserByUid($userId);
                            $feedid = (new FeedModel())->sendFeed($message, $apiInfo['name'], $userId, $user['username'], $user['nickname'], time());
                            $this->assign('ret', 0);
                            $this->assign('status', 'ok');
                            $this->assign('tid', $feedid);
                        } else {
                            $this->assign('ret', 2003);
                            $this->assign('status', 'invalid token');
                        }
                    } else {
                        $this->assign('ret', 2002);
                        $this->assign('status', 'permission denied');
                    }
                } else {
                    $this->assign('ret', 2001);
                    $this->assign('status', 'invalid appkey');
                }
            } else {
                $this->assign('ret', 1004);
                $this->assign('status', 'empty arguments');
            }
        }
        $this->render();
    }
}