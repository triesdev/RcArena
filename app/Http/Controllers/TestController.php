<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testCronAutoRejectTransactionWithPayment()
    {
        return 'testCronAutoRejectTransactionWithPayment';
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

        $data_updated = [];
        foreach ($transactions as $transaction) {
            $transaction->update(
                [
                    'transaction_status' => 'reject',
                ]
            );

            $data_updated[] = $transaction;

            // Update Payment Status Reject
            if ($transaction->payment) {
                $transaction->payment->update(
                    [
                        'payment_status' => 'reject',
                    ]
                );

                $data_updated[] = $transaction->payment;
            }
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data_updated
        ]);
    }
}
