<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 15:31
 */
class UserController extends Controller
{
    public function ac_login()
    {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $response = (new UserModel())->login($username, $password);

            if ($this->_mode == 0) {
                $this->assign("title", "登陆");
                switch ($response['ret']) {
                    case 0:
                        session_start();
                        $_SESSION['token'] = $response['token'];
                        header('Location: /index/index');
                    case 1001:
                        $this->assign('tp_error_msg', '密码错误');
                        break;
                    case 1002:
                        $this->assign('tp_error_msg', '用户名不存在');
                        break;
                }
            } else if ($this->_mode == 1) {
                $appKey = isset($_POST['appkey']) ? $_POST['appkey'] : '';
                $appSecret = isset($_POST['appsecret']) ? $_POST['appsecret'] : '';
                if (($apiInfo = (new ApiModel())->validate($appKey, $appSecret)) != null) {
                    if ($apiInfo['type'] == 1) {
                        if ($response['ret'] == 0) {
                            $appToken = (new TokenModel())->token($response['id'], $appKey);
                            $response['token'] = $appToken['token'];
                            $response['expire'] = $appToken['expire'];
                        }
                        $this->assignAll($response);
                    } else {
                        $this->assign('ret', 2002);
                        $this->assign('status', 'permission denied');
                    }
                } else {
                    $this->assign('ret', 2001);
                    $this->assign('status', 'invalid appkey');
                }
            }
        }
        $this->render();
    }

    public function ac_register()
    {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : $username;

            $response = (new UserModel())->register($username, $password, $email, $nickname);

            if ($this->_mode == 0) {
                $this->assign("title", "注册");
                switch ($response['ret']) {
                    case 0:
                        session_start();
                        $_SESSION['token'] = $response['token'];
                        header('Location: /index/index');
                    case 1003:
                        $this->assign('tp_error_msg', '用户名已存在');
                        break;
                    case 1004:
                        $this->assign('tp_error_msg', '参数不能为空');
                        break;
                    case 1005:
                        $this->assign('tp_error_msg', '用户名仅能为字母数字且长度大于4');
                        break;
                }
            } else if ($this->_mode == 1) {
                $appKey = isset($_POST['appkey']) ? $_POST['appkey'] : '';
                $appSecret = isset($_POST['appsecret']) ? $_POST['appsecret'] : '';
                if (($apiInfo = (new ApiModel())->validate($appKey, $appSecret)) != null) {
                    if ($apiInfo['type'] == 1) {
                        $this->assignAll($response);
                    } else {
                        $this->assign('ret', 2002);
                        $this->assign('status', 'permission denied');
                    }
                } else {
                    $this->assign('ret', 2001);
                    $this->assign('status', 'invalid appkey');
                }
            }
        }
        $this->render();
    }

    public function ac_logout()
    {
        session_start();
        unset($_SESSION['token']);
        header('Location: /user/login');
    }

    function ac_getidcode()
    {
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['']);
            if ($api['ret'] == 0) {
                $code = (new IdCodeModel())->getIdCode($api['uid']);
                $this->assign('ret', 0);
                $this->assign('status', 'ok');
                $this->assign('code', $code);
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }

    function ac_getinfo()
    {
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['']);
            if ($api['ret'] == 0) {
                $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
                $idcode = isset($_REQUEST['idcode']) ? $_REQUEST['idcode'] : '';
                if ($username == '' && $idcode == '') {
                    $response = (new UserModel())->getUserByUid($api['uid']);
                    $this->assign('ret', 0);
                    $this->assign('status', 'ok');
                    $this->assignAll($response);
                } else if ($idcode != '') {
                    $uid = (new IdCodeModel())->getUidByIdCode($idcode);
                    if ($uid != 0) {
                        $response = (new UserModel())->getUserByUid($uid);
                        $this->assign('ret', 0);
                        $this->assign('status', 'ok');
                        $this->assignAll($response);
                    } else {
                        $this->assign('ret', 1008);
                        $this->assign('status', 'invalid idcode');
                    }
                } else {
                    if ($row = (new UserModel())->getUserByUsername($username)) {
                        $this->assign('ret', 0);
                        $this->assign('status', 'ok');
                        $this->assignAll($row);
                    } else {
                        $this->assign('ret', 1005);
                        $this->assign('status', 'invalid username');
                    }
                }
            } else {
                $this->assignAll($api);
            }
        }
        $this->render();
    }

    public function ac_avatar($uid = 0)
    {
        $uid = isset($_GET['uid']) ? $_GET['uid'] : $uid;
        $username = isset($_GET['username']) ? $_GET['username'] : null;
        if (isset($_FILES['avatar'])) {
            if ($this->_mode == 1) {
                $api = $this->filter('Api', ['']);
                if ($api['ret'] == 0) {
                    if ((($_FILES["avatar"]["type"] == "image/gif") || ($_FILES["avatar"]["type"] == "image/jpeg") || ($_FILES["avatar"]["type"] == "image/pjpeg"))
                        && ($_FILES["avatar"]["size"] < 2000000)
                    ) {
                        $t = time() % 1000;
                        $this->storage()->upload("avatar/" . $api['uid'] . '_' . $t . ".jpg", $_FILES["avatar"]["tmp_name"]);
                        $url = $this->storage()->geturl("avatar/" . $api['uid'] . '_' . $t . ".jpg");
                        (new AvatarModel())->upload($api['uid'], $url);
                        $response = array('ret' => 0, 'status' => 'ok', 'url' => $url);
                    } else {
                        $response = array('ret' => 1007, 'status' => 'wrong file');
                    }
                    $this->assignAll($response);
                } else {
                    $this->assignAll($api);
                }
            }
            $this->render();
        } else {
            $imgUrl = "/static/images/avatar.jpg";
            if ($username != null) {
                if ($uid = (new UserModel())->where(["username = :username"], [':username' => $username])->fetch()['id']) {
                    if ($row = (new AvatarModel())->where(["uid = :id"], [':id' => $uid])->fetch()) {
                        $imgUrl = $row['url'];
                    }
                }
                Header("Location:$imgUrl");
            } else if ($uid != null) {
                if ($row = (new AvatarModel())->where(["uid = :id"], [':id' => $uid])->fetch()) {
                    $imgUrl = $row['url'];
                }
                Header("Location:$imgUrl");
            } else {
                Header("Location:$imgUrl");
            }
        }
    }
}