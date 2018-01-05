<?php
require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase as PHPUnit;
use Ruth\Router\Route;
use Ruth\Router\Dispatcher;

class DispatcherTest extends PHPUnit
{
    protected $dispatcher;

    public function tearDown()
    {
        $request = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/123'
        ];

        $_SERVER = array_merge($_SERVER, $request);

        Route::cleanRouteMap();

        Route::get('/:id', [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        $this->dispatcher = new Dispatcher(Route::getRouteMap());
    }

    public function testSmokeTestForDispatcher()
    {
        $this->assertEquals(true, class_exists(Dispatcher::class), 'It Should be return true if class exists');
    }

    public function testSmokeTestForGetExecuteMethod()
    {
        $this->assertInstanceOf(Ruth\Router\RouteExecution::class, $this->dispatcher->getExecute(), 'It Should be return true if class is even type of Ruth\Router\RouteExecution');
    }

    public function testSmokeTestForAllMethodsOfRouteExecution()
    {
        $routeExecution = $$this->dispatcher->getExecute();

        $this->assertEquals(true, method_exists($routeExecution, 'getRun'), "It should be return true if the getRun method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getAction'), "It should be return true if the getAction method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getParams'), "It should be return true if the getParams method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getPatterns'), "It should be return true if the getPatterns method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getError'), "It should be return true if the getError method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getRawRouteConfig'), "It should be return true if the getRawRouteConfig method exists");
    }

    public function testRetunsOfGetRunMethod()
    {
        $routeExecution = $$this->dispatcher->getExecute();

        $this->assertEquals('ControllerTest', $routeExecution->getRun(), "It should be return 'ControllerTest' string");
    }

    public function testRetunsOfGetActionMethod()
    {
        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals('testAction', $routeExecution->getAction(), "It should be return 'testAction' string");
    }
}
