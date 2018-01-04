<?php

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'route.php';

$routeMap = Ruth\Router\Route::getRouteMap();
$dispatcher = new Ruth\Router\Dispatcher($routeMap);

if (!$dispatcher->getExecute()->getError()) {
    $controller = $dispatcher->getExecute()->getRun();
    $action = $dispatcher->getExecute()->getAction();
    $params = $dispatcher->getExecute()->getParams();
    $patterns = $dispatcher->getExecute()->getPatterns();

    if (gettype($controller) == "string") {
        $controller = new $controller($params);
    } else {
        $controller->setParams($params);
        $controller->setPatterns($patterns);
    }

    $controller->$action();
} else {
    // error 404
    dump($dispatcher->getExecute()->getError());
}