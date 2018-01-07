<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/6
 * Time: 0:06
 */
class FriendController extends Controller
{
    function ac_list()
    {
        $state = isset($_REQUEST['state']) ? $_REQUEST['state'] : 2;
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['canGetFriend']);
            if ($api['ret'] == 0) {
                $this->assign('ret', 0);
                $this->assign('status', 'ok');
                $friends = (new FriendModel())->listFriend($api['uid'], $state);
                $this->assign('friends', $friends);
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }

    function ac_note()
    {
        if (isset($_POST['username']) && isset($_POST['notename'])) {
            $fusername = $_POST['username'];
            $fnotename = $_POST['notename'];
            if ($this->_mode == 1) {
                $api = $this->filter('Api', ['canGetFriend']);
                if ($api['ret'] == 0) {
                    $result = (new FriendModel())->noteFriend($api['uid'], $fusername, $fnotename);
                    $this->assignAll($result);
                } else {
                    $this->assignAll($api);
                }
            }
        }
        $this->render();
    }
}