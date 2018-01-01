<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 15:13
 */
class UserModel extends Model
{
    protected $table = 'tp_user';

    public function login($username, $password)
    {
        $user = $this->where(["email = ? or username = ?"], [$username, $username])->fetch();
        if ($user != null) {
            if ($user['password'] == md5($password)) {
                $timeline = time();
                $uid = $user['id'];
                if ($user['expire'] == null || $timeline > intval($user['expire'])) {
                    $token = md5($user['id'] . $user['username'] . $timeline);
                    $updatedata = array('token' => $token, 'expire' => $timeline + 604800);
                    $this->where(["id = :id"], [':id' => $uid])->update($updatedata);
                } else {
                    $token = $user['token'];
                }
                $response = array(
                    'ret' => 0,
                    'status' => 'ok',
                    'id' => $uid,
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'token' => $token,
                    'nickname' => $user['nickname']
                );
            } else {
                $response = array(
                    'ret' => 1001,
                    'status' => "password error"
                );
            }
        } else {
            $response = array(
                'ret' => 1002,
                'status' => "user not exists"
            );
        }
        return $response;
    }

    public function register($username, $password, $email, $nickname = '')
    {
        if (preg_match('/^[A-Za-z0-9_]+$/u', $username) && strlen($username) >= 4) {
            if ($this->where(["email = ? or username = ?"], [$username, $username])->fetch()) {
                $response = array(
                    'ret' => 1003,
                    'status' => "username exists"
                );
            } else {
                if ($nickname == '') {
                    $nickname = $username;
                }
                if ($password != '' && $email != '') {
                    $timeline = time();
                    $token = md5($password . $username . $timeline);
                    $datas = array(
                        'username' => $username,
                        'email' => $email,
                        'password' => md5($password),
                        'nickname' => $nickname,
                        'token' => $token,
                        'expire' => $timeline + 604800
                    );
                    if ($uid = $this->add($datas)) {
                        $response = array(
                            'ret' => 0,
                            'status' => 'ok',
                            'id' => $uid,
                            'username' => $username,
                            'email' => $email,
                            'token' => $token,
                            'nickname' => $nickname
                        );
                    }
                } else {
                    $response = array(
                        'ret' => 1004,
                        'status' => "empty arguments"
                    );
                }
            }
        } else {
            $response = array(
                'ret' => 1005,
                'status' => "invalid username"
            );
        }
        return $response;
    }

    public function check($token)
    {
        $user = $this->where(["token = ? and expire>UNIX_TIMESTAMP()"], [$token])->fetch();
        return $user;
    }

    public function getUserByUid($uid){
        return $this->where(["id = ?"], [$uid])->fetch();
    }
}