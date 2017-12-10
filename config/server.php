<?php

return [
    'daemonize' => false,
    'worker_num' => 8,
    'task_worker_num' => 0,
    'on_boot' => function ($server) {
        require APP_SRC_PATH.'/HttpHandler.php';
    },
    'handlers' => [
        HttpHandler::class,
    ],
];
