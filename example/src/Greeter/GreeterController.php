<?php

namespace Greeter;

class GreeterController
{
    public function helloAction()
    {
        $name = app('request')->input('name');
        $message = app('greeter')->hello($name);
        return response($message, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
