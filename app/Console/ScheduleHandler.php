<?php
namespace App\Console;
use App\Http\Controllers\Panel\TransactionController;
use App\Models\Event;
use App\Models\Transaction;

class ScheduleHandler {
    public function reminderEvent()
    {
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
    }

    // Reject Transaction with status pending and payment limit date less than current date
    public function rejectExpiredTransaction()
    {
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
        $transactionControllerPanel = new TransactionController();
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

            // Rollback Stock
            $transactionControllerPanel->rollbackStock($transaction->id);
        }
    }


}
