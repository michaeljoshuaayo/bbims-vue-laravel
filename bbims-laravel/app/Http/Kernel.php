<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // Other middleware...
        \Fruitcake\Cors\HandleCors::class,
    ];

    protected $commands = [
        \App\Console\Commands\UpdateExpiredBlood::class,
    ];

    // Other middleware groups and route middleware...

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('blood:update-expired')->everyMinute();
    }
}

