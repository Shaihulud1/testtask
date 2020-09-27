<?php

spl_autoload_register(function ($className) {
    $fileName =  __DIR__.'/'.$className.'.php';
    require_once $fileName;
});

$router = new app\Router;
$controller = new app\CoreController;

$router->addRouter(['method' => 'GET', 'action' => 'index', 'url' => '^\/$']);
$router->addRouter(['method' => 'POST', 'action' => 'uploadPhoto', 'url' => '^\/upload-photo$']);
$router->addRouter(['method' => 'GET',  'action' => 'getTaskStatus', 'url' => '^\/task\?task\_id=.+$']);

$existRoute = $router->existRoute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

if (!$existRoute || !method_exists($controller, $existRoute['action'])) {
    die('not found');
}
session_start();
$action = $existRoute['action'];
$controller->$action();


