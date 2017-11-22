<?php

use PHPUnit\Framework\TestCase as PHPUnit;
use Ruth\Router\Route;

class RouteTest extends PHPUnit
{

    public function tearDown()
    {
        Route::cleanRouteMap();
    }

    /**
     * @expectedException \Exception
     */
    public function testThrowExceptionIfTheRouteIsInvalid()
    {
        $inputValue = "home/test/";
        Route::normalizeRoute($inputValue);
    }

    public function testRemoveDoubleSalsh()
    {
        $expected = "/home/test/1";
        $inputValue = "//home//test///1";
        $outputValue = Route::normalizeRoute($inputValue);
        $this->assertEquals($expected, $outputValue, "should remove duplicate slashes");
    }

    public function testRemoveTheLastSalsh()
    {
        $expected = "/home/test/1";
        $inputValue = "//home//test///1/////";
        $outputValue = Route::normalizeRoute($inputValue);
        $this->assertEquals($expected, $outputValue, "should remove the last slash");
    }

    public function testRegisterRouteGet()
    {

        Route::get('/:id', [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\w+/'
            ]
        ]);

        $outputExpected = [
            "GET" => [
                2 => [
                    "/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "patterns" => [
                            ":id" => "/\w+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => ":id"
                        ]
                    ]
                ]
            ]
        ];

        $outputValue = Route::getRouteMap();

        $this->assertEquals(true, ($outputExpected === $outputValue), 'Should be a identity array');
    }

    public function testSetPatternsParamsWithOneOrMoreRoutes()
    {
        Route::get([
            '/home/:id' => 'home-id',
            '/test/:id'
            ], [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                'home-id:id' => '/\d+/',
                ':id' => '/\w+/'
            ]
        ]);

        $outputExpected = [
            "GET" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ],
                    "/test/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "patterns" => [
                            ":id" => "/\w+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "test",
                            2 => ":id"
                        ]
                    ]
                ]
            ]
        ];

        $outputValue = Route::getRouteMap();

        $this->assertEquals(true, ($outputExpected === $outputValue), 'Should be a identity array');
    }
}
