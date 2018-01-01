<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 15:31
 */
class UserController extends Controller
{
    public function login()
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

    public function register()
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

    public function logout()
    {
        session_start();
        unset($_SESSION['token']);
        header('Location: /user/login');
    }

    public function avatar()
    {
        $uid = isset($_GET['uid']) ? $_GET['uid'] : null;
        $username = isset($_GET['username']) ? $_GET['username'] : null;
        $imgUrl = "/static/images/avatar.jpg";
        if ($uid != null) {
            if ($row = (new AvatarModel())->where(["uid = :id"], [':id' => $uid])->fetch()) {
                $imgUrl = $row['url'];
            }
            Header("Location:$imgUrl");
        } else if ($username != null) {
            if ($uid = (new UserModel())->where(["username = :username"], [':username' => $username])->fetch()['id']) {
                if ($row = (new AvatarModel())->where(["uid = :id"], [':id' => $uid])->fetch()) {
                    $imgUrl = $row['url'];
                }
            }
            Header("Location:$imgUrl");
        } else {
            Header("Location:$imgUrl");
        }
    }
}