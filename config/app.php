<?php

return [
    'debug' => true,
    'env' => 'development',
    'log_file' => '/tmp/app.log',
    'log_level' => 'DEBUG',
    'providers' => [
        'greeter' => Greeter\GreeterServiceProvider::class,
    ],
    'middlewares' => [
        Greeter\GreeterMiddleware::class,
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
            'action' => Greeter\GreeterController::class.'@helloAction',
        ],
    ],
    'commands' => [
        // pass
    ],
];
