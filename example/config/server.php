<?php

return [
    'daemonize' => false,
    'worker_num' => 8,
    'task_worker_num' => 0,
    'on_boot' => function ($server) {
        require dirname(APP_PATH).'/src/HttpHandler.php';
    },
    'handlers' => [
        Cicada\HttpHandler::class,
    ],
];
