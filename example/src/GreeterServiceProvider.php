<?php

use Illuminate\Support\ServiceProvider;

class GreeterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->instance('greeter', $this);
    }

    public function hello($name = 'world')
    {
        return sprintf('hello %s', $name ?: 'world');
    }
}
