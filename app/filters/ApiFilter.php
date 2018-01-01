<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 23:26
 */
class ApiFilter extends Filter
{
    public function doFilter($appAuth = '')
    {
        if (isset($_POST['appkey']) && isset($_POST['token'])) {
            $appKey = $_POST['appkey'];
            $appToken = $_POST['token'];
            if ($apiInfo = (new ApiModel())->check($appKey)) {
                if ($apiInfo['type'] == 1 || $appAuth == '' || $apiInfo[$appAuth] == true) {
                    $userId = (new TokenModel())->check($appKey, $appToken);
                    if ($userId != 0) {
                        $response = array('ret' => 0, 'status' => 'ok', 'uid' => $userId, 'name' => $apiInfo['name']);
                    } else {
                        $response = array('ret' => 2003, 'status' => 'invalid token');
                    }
                } else {
                    $response = array('ret' => 2002, 'status' => 'permission denied');
                }
            } else {
                $response = array('ret' => 2001, 'status' => 'invalid appkey');
            }
        } else {
            $response = array('ret' => 1004, 'status' => 'empty arguments');
        }
        return $response;
    }
}