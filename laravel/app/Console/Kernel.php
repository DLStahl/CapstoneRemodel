<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateScheduleData::class,
        Commands\AutoAssign::class,
        Commands\PushSchedule::class,
        Commands\UpdateEvaluateData::class,
        Commands\InitiateEval::class,
        Commands\UpdateAnesthesiologistsData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('autoassign')->dailyAt('5:00');
        $schedule->command('update:anesthesiologists_data')->dailyAt('5:05');
        $schedule->command('update:schedule_data')->dailyAt('5:15');
        $schedule->command('update:evaluate_data')->dailyAt('5:30');
        $schedule->command('pushAPI')->dailyAt('5:45');
        $schedule->command('initiateEvals')->dailyAt('6:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
