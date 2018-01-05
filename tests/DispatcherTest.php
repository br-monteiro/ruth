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
        $expected = is_a($dispatcher->getExecute(), Ruth\Router\RouteExecution::class);

        $this->assertEquals(true, $expected, 'It Should be return true if class is even type of Ruth\Router\RouteExecution');
    }
}
