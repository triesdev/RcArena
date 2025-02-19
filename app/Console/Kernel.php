<?php

namespace App\Console;

use App\Http\Traits\FCM;
use App\Models\Event;
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

        // Reject Transaction with status pending and payment limit date less than current date
        $schedule->call(function () {
            $transactions = Transaction::with('payment')
                ->where(function ($query) {
                    // Melakukan Reject Untuk Transaction yang status baru dan process dengan status payment pending
                    $query->where('transaction_status', 'unpaid')
                        ->orWhere(function ($q){
                            $q->where('transaction_status', 'process')->whereHas('payment', function ($query) {
                                $query->where('payment_status', 'pending');
                            });
                        });
                })
                ->where('payment_limit_date', '<', now()->format('Y-m-d H:i:s'))
                ->get();

            // Update Transaction Status Reject
            foreach ($transactions as $transaction) {
                $transaction->update(
                    [
                        'transaction_status' => 'reject',
                    ]
                );

                // Update Payment Status Reject
                if ($transaction->payment) {
                    $transaction->payment->update(
                        [
                            'payment_status' => 'reject',
                        ]
                    );
                }
            }

        })->withoutOverlapping()->everyMinute()->name('reject_transaction');
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
