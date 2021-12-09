<?php
require_once 'config/global.php';
require_once 'vendor/autoload.php';
require_once 'controllers/GeneralController.php';

function controllerLoader($controller)
{
    $controller = ucwords($controller) . 'Controller';
    $path = 'controllers/' . $controller . '.php';
    if (!file_exists($path)) {
        $path = 'controllers/' . ucwords(PAGEHANDLER) . 'Controller.php';
    }
    require_once $path;
    return new $controller();
}

function actionLoader($controllerObj)
{
    if (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING)) {
        $controllerObj->run(filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING));
    } else {
        $controllerObj->run(DEFAULT_PAGE);
    }
}

if (filter_input(INPUT_GET, 'controller', FILTER_SANITIZE_STRING)) {
    $controllerObj = controllerLoader(filter_input(INPUT_GET, 'controller', FILTER_SANITIZE_STRING));
    actionLoader($controllerObj);
} else {
    $controllerObj = controllerLoader(PAGEHANDLER);
    actionLoader($controllerObj);
}
