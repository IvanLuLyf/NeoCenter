<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/7
 * Time: 4:51
 */
class NotifyController extends Controller
{
    function ac_view()
    {
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['canGetFriend']);
            if ($api['ret'] == 0) {
                $this->assign('ret', 0);
                $this->assign('status', 'ok');
                $notice = (new NotificationModel())->getNotice($api['uid']);
                $this->assign('notifications', $notice);
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }
}