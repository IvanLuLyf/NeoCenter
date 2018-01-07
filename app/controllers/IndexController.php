<?php
/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/6
 * Time: 3:45
 */
class IndexController extends Controller
{
    public function ac_index()
    {
        $user = $this->filter("Auth");
        if ($user != null) {
            header('Location: /post/index');
        } else {
            header('Location: /user/login');
        }
    }
}