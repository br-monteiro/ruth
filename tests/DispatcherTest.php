<?php
require_once 'vendor/autoload.php';

use PHPUnit\Framework\TestCase as PHPUnit;
use Ruth\Router\Route;
use Ruth\Router\Dispatcher;

class DispatcherTest extends PHPUnit
{

    protected $dispatcher;

    public function setUp()
    {
        $request = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/123'
        ];

        $_SERVER = array_merge($_SERVER, $request);

        Route::get('/:id', [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        $this->dispatcher = new Dispatcher(Route::getRouteMap());
    }

    public function tearDown()
    {
        Route::cleanRouteMap();
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
        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals(true, method_exists($routeExecution, 'getRun'), "It should be return true if the getRun method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getAction'), "It should be return true if the getAction method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getParams'), "It should be return true if the getParams method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getPatterns'), "It should be return true if the getPatterns method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getError'), "It should be return true if the getError method exists");
        $this->assertEquals(true, method_exists($routeExecution, 'getRawRouteConfig'), "It should be return true if the getRawRouteConfig method exists");
    }

    public function testRetunsOfGetRunMethod()
    {
        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals('ControllerTest', $routeExecution->getRun(), "It should be return 'ControllerTest' string");
    }

    public function testRetunsOfGetActionMethod()
    {
        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals('testAction', $routeExecution->getAction(), "It should be return 'testAction' string");
    }

    public function testRetunsOfGetParamsMethod()
    {
        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals(['id' => 123], $routeExecution->getParams(), "It should be return an array with '['id' => 123]' content");
    }

    public function testRetunsOfGetPatternsMethod()
    {
        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals([':id' => '/\d+/'], $routeExecution->getPatterns(), "It should be return an array with '[':id' => '/\d+/']' content");
    }

    public function testRetunsOfGetErrorMethod()
    {
        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals(null, $routeExecution->getError(), "It should be return null");
    }

    public function testRetunsOfGetErrorMethodWhenRouteIsNotMatch()
    {
        $request = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/deve-haver-erro'
        ];

        $expectedRawConfig = [
            "patterns" => [
                ":id" => "/\d+/"
            ],
            "explode" => [
                0 => "",
                1 => ":id"
            ],
            "type" => "GET",
            "error" => "route not found"
        ];

        $_SERVER = array_merge($_SERVER, $request);

        $dispatcher = new Dispatcher(Route::getRouteMap());
        $routeExecution = $dispatcher->getExecute();

        $this->assertEquals(null, $routeExecution->getRun(), "It should be return null when route is fail");
        $this->assertEquals(null, $routeExecution->getAction(), "It should be return null when route is fail");
        $this->assertEquals(null, $routeExecution->getParams(), "It should be return null when route is fail");
        $this->assertEquals($expectedRawConfig['patterns'], $routeExecution->getPatterns(), "It should be return '[':id' => '/\d+/']' when route is fail");
        $this->assertEquals($expectedRawConfig, $routeExecution->getRawRouteConfig(), "It should be return an equal array");

        $this->assertEquals('route not found', $routeExecution->getError(), "It should be return 'route not found' string");
    }

    public function testRetunsOfGetRawRouteConfigMethod()
    {
        $expectedRawRouteExecution = [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/',
            ],
            'explode' => [
                0 => '',
                1 => ':id',
            ],
            'type' => 'GET',
            'params' => [
                'id' => '123',
            ],
        ];

        $routeExecution = $this->dispatcher->getExecute();

        $this->assertEquals($expectedRawRouteExecution, $routeExecution->getRawRouteConfig(), "It should be return an equal array");
    }
}
