<?php

namespace Cicada;

use Laravel\Lumen\Application as BaseApplication;
use Symfony\Component\HttpFoundation\Request;

class Application extends BaseApplication
{
    public function __construct($basePath = null)
    {
        parent::__construct($basePath);
        $this->singleton('Illuminate\Contracts\Debug\ExceptionHandler', ExceptionHandler::class);
        $this->singleton('Illuminate\Contracts\Console\Kernel', Kernel::class);
        $this->configure('app');
        $this->configureMonologUsing([$this, 'initLogger']);
        $this->registerProviders();
        $this->addMiddlewares();
        $this->addRoutes();
    }

    public function prepareForConsoleCommand($aliases = true)
    {
        // pass
    }

    protected function initLogger($logger)
    {
        $file = $this->config['app.log_file'] ?: storage_path('logs/lumen.log');
        if (! is_dir($logDir = dirname($file))) {
            mkdir($logDir, 0777, true);
        }

        $level = $this->config['app.log_level'] ?: 'DEBUG';
        $handler = new LogHandler($file, $level);
        $logger->pushHandler($handler);
        return $logger;
    }

    protected function addRoutes()
    {
        $defaultMethods = [
            Request::METHOD_HEAD,
            Request::METHOD_GET,
            Request::METHOD_POST,
            Request::METHOD_PUT,
            Request::METHOD_PATCH,
            Request::METHOD_DELETE,
            Request::METHOD_PURGE,
            Request::METHOD_OPTIONS,
            Request::METHOD_TRACE,
            Request::METHOD_CONNECT,
        ];

        $routes = (array) $this->config['app.routes'];
        foreach ($routes as $route) {
            if (isset($route['uri']) && isset($route['action'])) {
                if (! isset($route['method'])) {
                    $route['method'] = $defaultMethods;
                }
                $this->router->addRoute($route['method'], $route['uri'], $route['action']);
            }
        }
    }

    protected function addMiddlewares()
    {
        $middlewares = (array) $this->config['app.middlewares'];
        foreach ($middlewares as $name => $middleware) {
            if (is_string($name)) {
                $this->routeMiddleware([$name => $middleware]);
            } else {
                $this->middleware([$middleware]);
            }
        }
    }

    protected function registerProviders()
    {
        $providers = (array) $this->config['app.providers'];
        foreach (array_keys($providers) as $abstract) {
            $this->availableBindings[$abstract] = 'registerAppBindings_'.$abstract;
        }
    }

    public function __call($method, $args)
    {
        if (strpos($method, 'registerAppBindings_') === 0) {
            $abstract = substr($method, strlen('registerAppBindings_'));
            $provider = $this->config['app.providers.'.$abstract];
            if ($provider) {
                if (is_callable($provider)) {
                    call_user_func($provider, $this);
                } else {
                    $this->singleton($abstract, function ($app) use ($abstract, $provider) {
                        $provider = (array) $provider;
                        return $this->loadComponent(
                            isset($provider[1]) ? $provider[1] : $abstract,
                            isset($provider[0]) ? $provider[0] : '',
                            $abstract
                        );
                    });
                }
            }
        } else {
            throw new ErrorException(sprintf(
                'Call to undefined method %s::%s()',
                __CLASS__,
                $method
            ));
        }
    }
}
