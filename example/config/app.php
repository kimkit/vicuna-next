<?php

return [
    'debug' => true,
    'env' => 'development',
    'log_file' => '/tmp/app.log',
    'log_level' => 'DEBUG',
    'providers' => [
        'greeter' => GreeterServiceProvider::class,
    ],
    'middlewares' => [
        GreeterMiddleware::class,
    ],
    'routes' => [
        [
            'uri' => '/',
            'action' => function () {
                return response(app()->version(), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
            },
        ],
        [
            'uri' => '/hello',
            'action' => GreeterController::class.'@helloAction',
        ],
    ],
    'commands' => [
        // pass
    ],
];
