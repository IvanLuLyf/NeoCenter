<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/8
 * Time: 11:44
 */
class TauthController extends Controller
{
    public function ac_getappname()
    {
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['']);
            if ($api['ret'] == 0) {
                if (isset($_POST['ak']) && $app = (new ApiModel())->check($_POST['ak'])) {
                    $this->assign('ret', 0);
                    $this->assign('status', 'ok');
                    $this->assign('appname', $app['name']);
                } else {
                    $this->assign('ret', 2001);
                    $this->assign('status', 'invalid appkey');
                }
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }

    function ac_getcode()
    {
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['']);
            if ($api['ret'] == 0) {
                if (isset($_POST['ak']) && $app = (new ApiModel())->check($_POST['ak'])) {
                    $timeline = time();
                    $code = (new TauthCodeModel())->getCode($_POST['ak'], $app['id'], $api['uid'], $timeline);
                    $this->assign('ret', 0);
                    $this->assign('status', 'ok');
                    $this->assign('code', $code);
                    $this->assign('expire', ($timeline + 604800));
                } else {
                    $this->assign('ret', 2001);
                    $this->assign('status', 'invalid appkey');
                }
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }

    function ac_gettoken()
    {
        if (isset($_POST['appkey']) && isset($_POST['appsecret']) && $app = (new ApiModel())->validate($_POST['appkey'], $_POST['appsecret'])) {
            if (isset($_POST['code']) && $uid = (new TauthCodeModel())->checkCode($app['id'], $_POST['code'])) {
                $token = (new TokenModel())->token($uid, $_POST['appkey']);
                $this->assign('ret', 0);
                $this->assign('status', 'ok');
                $this->assignAll($token);
            } else {
                $this->assign('ret', 2003);
                $this->assign('status', 'invalid code');
            }
        } else {
            $this->assign('ret', 2001);
            $this->assign('status', 'invalid appkey');
        }
        $this->render();
    }
}