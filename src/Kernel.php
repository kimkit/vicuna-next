<?php

namespace Cicada;

use Laravel\Lumen\Console\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    protected function getCommands()
    {
        return (array) $this->app['config']['app.commands'];
    }

    protected function defineConsoleSchedule()
    {
        // pass
    }
}
