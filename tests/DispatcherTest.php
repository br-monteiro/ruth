<?php
require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase as PHPUnit;
use Ruth\Router\Route;
use Ruth\Router\Dispatcher;

class DispatcherTest extends PHPUnit
{

    public function tearDown()
    {
        Route::cleanRouteMap();

        $request = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/123'
        ];

        $_SERVER = array_merge($_SERVER, $request);
    }

    public function testSmokeTestForDispatcher()
    {
        $this->assertEquals(true, class_exists(Dispatcher::class), 'It Should be return true if class exists');
    }

    public function testSmokeTestForGetExecuteMethod()
    {
        Route::get('/:id', [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        $dispatcher = new Dispatcher(Route::getRouteMap());

        $this->assertInstanceOf(Ruth\Router\RouteExecution::class, $dispatcher->getExecute(), 'It Should be return true if class is even type of Ruth\Router\RouteExecution');
    }

    public function testSmokeTestForAllMethodsOfRouteExecution()
    {
        Route::get('/:id', [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        $dispatcher = new Dispatcher(Route::getRouteMap());
        $routeExecution = $dispatcher->getExecute();

        $this->assertEquals(true, method_exists($routeExecution, 'getRun'), "It should be return true if the getRun method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getAction'), "It should be return true if the getAction method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getParams'), "It should be return true if the getParams method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getPatterns'), "It should be return true if the getPatterns method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getError'), "It should be return true if the getError method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getRawRouteConfig'), "It should be return true if the getRawRouteConfig method exists");
    }
}
