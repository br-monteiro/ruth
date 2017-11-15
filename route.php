<?php

Ruth\Router\Route::get(
    [
    '/home/:id/teste' => 'home-teste',
    '/home/:id/'
    ], [
    'run' => App\Controllers\TesteController::class,
    'action' => 'teste',
    'patterns' => [
        'home-teste:id' => '/[a-z]+/',
        ':id' => '/\d/'
    ]
]);

Ruth\Router\Route::get(['/', '/:id'], [
    'run' => App\Controllers\TesteController::class,
    'action' => 'teste',
    'patterns' => [
        ':id' => '/\w+/'
    ]
]);

Ruth\Router\Route::many(['post', 'DELETE'], '/teste', [
    'run' => App\Controllers\TesteController::class,
    'action' => 'teste',
    'patterns' => [
        ':id' => '/\w+/'
    ]
]);
