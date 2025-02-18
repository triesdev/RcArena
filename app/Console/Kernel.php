<?php

namespace App\Console;

use App\Http\Traits\FCM;
use App\Models\Event;
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
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $events = Event::whereDate('event_date', date('Y-m-d'))->first();

            if (count($events) > 0) {
                foreach ($events as $event) {
                    $this->sendNotifByTopic('general_info',
                        [
                            "title"        => "",
                            "message"      => "",
                            "page_route"   => "",
                            "reference_id" => "",
                        ]);
                }
            }
        })->dailyAt('05:00');
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
