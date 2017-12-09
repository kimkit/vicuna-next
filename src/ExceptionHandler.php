<?php

namespace Cicada;

use Laravel\Lumen\Exceptions\Handler as BaseExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandler extends BaseExceptionHandler
{
    protected $dontReport = [
        NotFoundHttpException::class,
    ];
}
