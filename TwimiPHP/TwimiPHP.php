<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 14:44
 */
class TwimiPHP
{
    protected $config = [];
    protected $mode = 0;

    public function __construct($config, $m = 0)
    {
        $this->config = $config;
        $this->mode = $m;
    }

    public function run()
    {
        spl_autoload_register(array($this, 'loadClass'));
        $this->setReporting();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->LoadConfig();
        $this->route();
    }

    public function route()
    {
        $controllerName = isset($_GET['mod']) ? ucfirst($_GET['mod']) : $this->config['defaultController'];
        $actionName = isset($_GET['action']) ? $_GET['action'] : $this->config['defaultAction'];
        $param = array();
        $url = $_SERVER['REQUEST_URI'];
        $position = strpos($url, '?');
        $url = ($position === false) ? $url : substr($url, 0, $position);
        $url = trim($url, '/');
        if ($url && strtolower($url) != "index.php" && $this->mode == 0) {
            $urlArray = explode('/', $url);
            $urlArray = array_filter($urlArray);
            if (strtolower($urlArray[0]) == "api") {
                array_shift($urlArray);
                $this->mode = 1;
            }
            $controllerName = ucfirst($urlArray[0]);
            array_shift($urlArray);
            $actionName = $urlArray ? $urlArray[0] : $actionName;
            array_shift($urlArray);
            $param = $urlArray ? $urlArray : array();
        } elseif ($this->mode == 1) {
            $param = array();
        }
        $controller = $controllerName . 'Controller';
        if (!class_exists($controller)) {
            exit($controller . ' Not Found');
        }
        if (!method_exists($controller, 'ac_' .$actionName)) {
            exit($actionName . ' Not Exist');
        }
        $dispatch = new $controller($controllerName, $actionName, $this->mode);
        call_user_func_array(array($dispatch, 'ac_' . $actionName), $param);
    }

    public function setReporting()
    {
        if (APP_DEBUG === true) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'On');
        }
    }

    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    public function unregisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    public function LoadConfig()
    {
        if ($this->config['db']) {
            define('DB_HOST', $this->config['db']['host']);
            define('DB_NAME', $this->config['db']['dbname']);
            define('DB_USER', $this->config['db']['username']);
            define('DB_PASS', $this->config['db']['password']);

            define('TP_STORAGE', $this->config['defaultStorage']);

            define("TP_SITENAME", $this->config['sitename']);
            define("TP_SITEURL", $this->config['siteurl']);
        }
    }

    public static function loadClass($class)
    {
        $frameworks = __DIR__ . '/' . $class . '.php';
        $controllers = APP_PATH . 'app/controllers/' . $class . '.php';
        $models = APP_PATH . 'app/models/' . $class . '.php';
        $filters = APP_PATH . 'app/filters/' . $class . '.php';
        $storage = __DIR__ . '/Storage/' . $class . '.php';
        if (file_exists($frameworks)) {
            include $frameworks;
        } elseif (file_exists($controllers)) {
            include $controllers;
        } elseif (file_exists($models)) {
            include $models;
        } elseif (file_exists($filters)) {
            include $filters;
        } elseif (file_exists($storage)) {
            include $storage;
        } else {
            // 错误代码
        }
    }
}