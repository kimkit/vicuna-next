<?php

return [
    'on_boot' => function ($server) {
        require dirname(APP_PATH).'/src/HttpHandler.php';
    },
    'handlers' => [
        Cicada\HttpHandler::class,
    ],
];
