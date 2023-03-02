<?php

namespace App\Console;

use Illuminate\Support\Carbon;
use App\Models\UserValidationCode;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            UserValidationCode::where('expires_at', '<=', Carbon::now()->toDateTimeString())->delete();
        })->everyMinute();
    }
}
