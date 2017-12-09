<?php

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Vicuna\Handler;

class HttpHandler extends Handler
{
    public function onInit($server)
    {
        defined('APP_PATH') || define('APP_PATH', getcwd());
        defined('APP_SRC_PATH') || define('APP_SRC_PATH', APP_PATH.'/src');
        defined('APP_CONFIG_PATH') || define('APP_CONFIG_PATH', APP_PATH.'/config');
        defined('VENDOR_PATH') || define('VENDOR_PATH', APP_PATH.'/vendor');

        if (! is_file(VENDOR_PATH.'/autoload.php')) {
            throw new Exception('VENDOR_PATH('.VENDOR_PATH.') invalid');
        }
    }

    public function onStart($server)
    {
        $server->log('INFO', 'APP_PATH='.APP_PATH);
        $server->log('INFO', 'APP_SRC_PATH='.APP_SRC_PATH);
        $server->log('INFO', 'APP_CONFIG_PATH='.APP_CONFIG_PATH);
        $server->log('INFO', 'VENDOR_PATH='.VENDOR_PATH);
    }

    public function onWorkerStart($server)
    {
        $classLoader = include VENDOR_PATH.'/autoload.php';
        $classLoader->addPsr4('', APP_SRC_PATH);

        new Application(APP_PATH);
        app()->instance('swoole.server', $server);
    }

    public function onRequest($server, $request, $response)
    {
        app()->instance('swoole.request', $request);
        app()->instance('swoole.response', $response);

        foreach ($request->server() as $k => $v) {
            $_SERVER[$k] = $v;
        }

        $this->writeResponse(app()->handle($this->buildRequest($request)));
    }

    protected function buildRequest($request)
    {
        return new Request(
            $request->get(),
            $request->post(),
            [],
            $request->cookie(),
            $request->files(),
            $request->server(),
            $request->rawContent()
        );
    }

    protected function writeResponse($data)
    {
        if (env('APP_DEBUG', config('app.debug', false))) {
            $close = "\e[0m";
            $red = "\e[31m";
            $green = "\e[32m";

            $statusCode = ($data instanceof Response) ? $data->getStatusCode() : 200;

            app('swoole.server')->log('DEBUG', sprintf(
                "[ %s%d%s ] %0.6f | %s %s",
                ($statusCode == 200) ? $green : $red,
                $statusCode,
                $close,
                app('swoole.request')->executeTime(),
                app('swoole.request')->method(),
                app('swoole.request')->uri()
            ));
        }

        $response = app('swoole.response');
        if ($response->hasOutput()) {
            return;
        }

        if ($data instanceof Response) {
            foreach ($data->headers->allPreserveCase() as $name => $values) {
                foreach ($values as $value) {
                    $response->header($name, $value);
                }
            }
            foreach ($data->headers->getCookies() as $cookie) {
                $response->cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    $cookie->isSecure(),
                    $cookie->isHttpOnly()
                );
            }
            $response->status($data->getStatusCode());
            if ($data instanceof BinaryFileResponse) {
                $response->sendfile($data->getFile()->getPathname());
            } else {
                $response->send($data->getContent());
            }
        } else {
            $response->send((string) $data);
        }
    }
}
