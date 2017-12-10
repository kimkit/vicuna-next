<?php

namespace Greeter;

use Closure;

class GreeterMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Middleware-Name', 'greeter');
        return $response;
    }
}
