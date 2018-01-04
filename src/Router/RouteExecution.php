<?php
namespace Ruth\Router;

class RouteExecution
{

    private $run;
    private $action;
    private $params = [];
    private $patterns;
    private $error;
    private $rawRouteConfig;

    public function __construct(array $route)
    {
        $this->config($route);
    }

    private function config($route)
    {
        $this->run = $route['run'] ?? null;
        $this->action = $route['action'] ?? null;
        $this->params = $route['params'] ?? null;
        $this->patterns = $route['patterns'] ?? null;
        $this->error = $route['error'] ?? null;
        $this->rawRouteConfig = $route;
    }

    public function getRun()
    {
        return $this->run;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getPatterns()
    {
        return $this->patterns;
    }
    
    public function getError()
    {
        return $this->error;
    }
    
    public function getRawRouteConfig()
    {
        return $this->rawRouteConfig;
    }
}
