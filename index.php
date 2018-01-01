<?php
/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 14:42
 */

define('APP_PATH', __DIR__ . '/');
define('APP_DEBUG', true);
define("IN_TWIMI_PHP", "True", TRUE);
require(APP_PATH . 'TwimiPHP/TwimiPHP.php');
$config = require(APP_PATH . 'config/config.php');
(new TwimiPHP($config))->run();