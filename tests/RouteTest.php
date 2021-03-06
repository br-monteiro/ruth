<?php

require_once 'vendor/autoload.php';

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

    public function testRemoveDoubleSlash()
    {
        $expected = "/home/test/1";
        $inputValue = "//home//test///1";
        $outputValue = Route::normalizeRoute($inputValue);
        $this->assertEquals($expected, $outputValue, "should remove duplicate slashes");
    }

    public function testRemoveTheLastSlash()
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
                        "type" => "GET",
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

        $this->assertEquals($outputExpected, $outputValue, 'Should be an equal array');
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
                        "type" => "GET",
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
                        "type" => "GET",
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

        $this->assertEquals($outputExpected, $outputValue, 'Should be an equal array');
    }

    public function testSetEqualsPatternsParamsToOneOrMoreRoutesWithEvenParamName()
    {
        Route::get([
            '/home/:id',
            '/test/:id'
            ], [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        $outputExpected = [
            "GET" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "GET",
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
                        "type" => "GET",
                        "patterns" => [
                            ":id" => "/\d+/"
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

        $this->assertEquals($outputExpected, $outputValue, 'Should be an equal array');
    }

    public function testReturnArrayWithoutRemovedRoutes()
    {
        Route::get([
            '/home/:id',
            '/test/:id'
            ], [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        Route::removeRoute([
            "get" => "/test/:id"
        ]);

        $outputExpected = [
            "GET" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "GET",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ]
                ]
            ]
        ];

        $outputValue = Route::getRouteMap();

        $this->assertEquals($outputExpected, $outputValue, 'Should be an equal array');
    }

    public function testRemoveAllRoutesEvenMethodAndLength()
    {
        Route::get([
            '/home/:id',
            '/test/:id'
            ], []);

        Route::removeRoute([
            "GET" => [
                "/home/:id",
                "/test/:id"
            ]
        ]);

        $outputValue = Route::getRouteMap();
        $outputValue = empty($outputValue['GET']);

        $this->assertEquals(true, $outputValue, 'Should be returned an empty array');
    }

    public function testCleanRouteMap()
    {
        Route::get([
            '/home/:id',
            '/test/:id'
            ], []);

        Route::cleanRouteMap();

        $outputValue = Route::getRouteMap();

        $this->assertEquals([], $outputValue, 'Should be returned an empty array');
    }

    public function testRegisterRoutesForAllMethodsAllowed()
    {
        Route::all('/home/:id', [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        $outputExpected = [
            "GET" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "GET",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ]
                ]
            ],
            "POST" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "POST",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ]
                ]
            ],
            "PUT" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "PUT",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ]
                ]
            ],
            "DELETE" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "DELETE",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ]
                ]
            ]
        ];

        $outputValue = Route::getRouteMap();

        $this->assertEquals($outputExpected, $outputValue, 'Should be an equal array');
    }

    public function testRegisterRoutesForManyMethodsAllowed()
    {
        Route::many(['GET', 'PUT'], '/home/:id', [
            'run' => 'ControllerTest',
            'action' => 'testAction',
            'patterns' => [
                ':id' => '/\d+/'
            ]
        ]);

        $outputExpected = [
            "GET" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "GET",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ]
                ]
            ],
            "PUT" => [
                3 => [
                    "/home/:id" => [
                        "run" => "ControllerTest",
                        "action" => "testAction",
                        "type" => "PUT",
                        "patterns" => [
                            ":id" => "/\d+/"
                        ],
                        "explode" => [
                            0 => "",
                            1 => "home",
                            2 => ":id"
                        ]
                    ]
                ]
            ]
        ];

        $outputValue = Route::getRouteMap();

        $this->assertEquals($outputExpected, $outputValue, 'Should be an equal array');
    }

    /**
     * @expectedException \Exception
     */
    public function testThrowExceptionIfTryRemoveAnUnknowRoute()
    {
        Route::get('/home/:id', []);

        Route::removeRoute([
            "get" => "/test/:id"
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testThrowExceptionIfTryRemoveAnRouteOfUnknowMethod()
    {
        Route::get('/home/:id', []);

        Route::removeRoute([
            "UNKNOW" => "/test/:id"
        ]);
    }
}
