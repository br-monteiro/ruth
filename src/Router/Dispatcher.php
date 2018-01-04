<?php
namespace Ruth\Router;

use Ruth\Router\Route;
use Ruth\Router\RouteExecution;

class Dispatcher
{

    private $routeMap = [];
    private $method;
    private $uri;
    private $routeExecution;

    public function __construct(array $routeMap)
    {
        $this->routeMap = $routeMap;
        $this->config()
            ->findRoute();
    }

    private function config(): Dispatcher
    {
        $this->method = strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
        $this->uri = Route::normalizeRoute(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        return $this;
    }

    private function findRoute()
    {
        $length = Route::lengthRoute($this->uri);
        $currentRoute = null;

        if (isset($this->routeMap[$this->method][$length])) {
            foreach ($this->routeMap[$this->method][$length] as $config) {
                $currentRoute = $config;
                $validUri = $this->validateUri($config);
                if (gettype($validUri) != "array") {
                    continue;
                }
                $config['params'] = $validUri;
                $this->routeExecution = new RouteExecution($config);
                // stop execution
                return;
            }
        }

        // remove unnecessary indexs
        unset($currentRoute['action'], $currentRoute['run']);
        // set error
        $currentRoute['error'] = 'route not found';
        $this->routeExecution = new RouteExecution($currentRoute);
    }

    private function validateUri(array $config)
    {
        $uriExplode = explode('/', $this->uri);
        $uriParams = [];

        foreach ($config['explode'] as $key => $value) {
            if (preg_match('/:\w+/', $value) && preg_match($config['patterns'][$value], $uriExplode[$key])) {
                $paramName = str_replace(':', '', $value);
                $uriParams[$paramName] = $uriExplode[$key];
                continue;
            }
            if (!isset($uriExplode[$key]) || $uriExplode[$key] != $value) {
                return false;
            }
        }

        return $uriParams;
    }

    public function getExecute(): RouteExecution
    {
        return $this->routeExecution;
    }
}
