<?php

namespace App\Console;

use App\Http\Traits\FCM;
use App\Models\Event;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    use FCM;

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $schedule_class = new ScheduleHandler();
            $schedule_class->reminderEvent();
        })->dailyAt('05:00');

        $schedule->call(function () {
            $schedule_class = new ScheduleHandler();
            $schedule_class->rejectExpiredTransaction();
        })
            ->name('reject_transaction')
            ->withoutOverlapping()
            ->everyMinute();
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
