<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 17:13
 */
class TestController extends Controller
{
    public function dosth()
    {
        if ($this->_mode == 1) {
            $appKey = $_POST['appkey'];
            $appToken = $_POST['token'];
            $apiInfo = (new ApiModel())->check($appKey);
            $userId = (new TokenModel())->check($appKey,$appToken);
            $feed = (new FeedModel())->listFeed($userId);
            $this->assign("api",$apiInfo);
            $this->assign("uid",$userId);
            $this->assign("feed",$feed);
            $this->render();
        }
    }
}