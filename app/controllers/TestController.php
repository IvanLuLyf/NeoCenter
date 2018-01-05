<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 17:13
 */
class TestController extends Controller
{
    public function ac_dosth()
    {
        if ($this->_mode == 1) {
            $appKey = $_POST['appkey'];
            $appToken = $_POST['token'];
            $apiInfo = (new ApiModel())->check($appKey);
            $userId = (new TokenModel())->check($appKey, $appToken);
            $feed = (new FeedModel())->listFeed($userId);
            $this->assign("api", $apiInfo);
            $this->assign("uid", $userId);
            $this->assign("feed", $feed);
            $this->render();
        }
    }

    public function ac_dos()
    {
        if ($this->_mode == 1) {
            $api = $this->filter('Api', ['canFeed']);
            if ($api['ret'] == 0) {
                $url = (new AvatarModel())->getAvatar($api['uid']);
                $this->assign('url', $url);
            } else {
                $this->assignAll($api);
            }
            $this->render();
        }
    }

    public function ac_test($uid = 0)
    {
        echo '<a>' . $uid . '</a>';
    }

    public function ac_mf()
    {
        if (isset($_FILES['attach'])) {
            $names = $_FILES['attach']['name'];
            if (is_array($names)) {
                for ($i = 0; $i < count($names); $i++) {
                    echo $names[$i] . '<br>';
                }
            } else {
                    echo $names;
            }
        }else{
            echo 'no file';
        }
    }
}