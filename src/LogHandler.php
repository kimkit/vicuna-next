<?php

use Monolog\Handler\AbstractHandler;
use Monolog\Logger;

class LogHandler extends AbstractHandler
{
    protected $file;

    public function __construct($file, $level = Logger::DEBUG, $bubble = true)
    {
        $this->file = $file;
        parent::__construct($level, $bubble);
    }

    public function handle(array $record)
    {
        $record['context'] += (array) app('config')['app.log_context'];
        $record['context'] += $record['extra'];

        $line = sprintf(
            "[%s] %s %s\n",
            $record['datetime']->format('Y-m-d H:i:s'),
            $record['level_name'],
            json_encode([
                'channel' => $record['channel'],
                'message' => $record['message'],
                'context' => $record['context'],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $file = $this->file.'.'.date('Y-m-d');

        if (function_exists('swoole_async_writefile') && app('config')['app.log_async']) {
            swoole_async_writefile($file, $line, null, FILE_APPEND);
        } else {
            file_put_contents($file, $line, FILE_APPEND);
        }
    }
}
