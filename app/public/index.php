<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__));

spl_autoload_register(static function ($className) {
    require_once str_replace('\\', DIRECTORY_SEPARATOR, lcfirst($className)) . '.php';
});

//TODO: DEV mode only
include 'debug.php';

$config = require 'config.php';

$app = new \Core\App($config);
$router = $app->init()->getRouter();

$response = $router->route();
$response->send();
