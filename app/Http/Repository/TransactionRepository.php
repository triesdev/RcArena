<?php

namespace App\Http\Repository;
use App\Http\Controllers\ApiController;
use App\Models\Transaction;

class TransactionRepository extends ApiController
{
    public function getDetailTransactionById($id, $panel = false)
    {
        $transaction = Transaction::with(['transaction_details' => function ($q) {
            $q->leftJoin('tickets', 'transaction_details.ticket_id', '=', 'tickets.id')
                ->leftJoin('classes', 'tickets.class_id', '=', 'classes.id')
                ->select(
                    'transaction_details.id',
                    'transaction_details.transaction_id',
                    'transaction_details.qty',
                    'transaction_details.price',
                    'transaction_details.subtotal_price',
                    'tickets.name as ticket_name',
                    'classes.name as class_name',
                    'classes.id as class_id',
                );
        },'transaction_detail_class_groups'])
            ->leftJoin('events', 'transactions.event_id', '=', 'events.id')
            ->select('transactions.*', 'events.name as event_name','payments.payment_proof_image_uri','payments.payment_status')
            ->leftJoin('payments', 'transactions.id', '=', 'payments.transaction_id');
        if ($panel) {
            $transaction = $transaction->with('user');
            $transaction = $transaction->with('payment');
        }

        $transaction = $transaction->find($id);

        if (!$transaction) {
            return $this->errorResponse("Data not found");
        }

        // Convert Data
        $data = $transaction->transaction_detail_class_groups->map(function ($group) use ($transaction) {
            $group->transaction_details = $transaction->transaction_details->filter(function ($detail) use ($group) {
                return $detail->class_id === $group->class_id;
            })->flatten();

            return $group;
        });

        //Remove Transaction Detail Class Group
        unset($transaction->transaction_detail_class_groups);
        unset($transaction->transaction_details);

        // Change Transaction Details With Converted Data
        $transaction->transaction_details = $data;

        return $transaction;
    }
}
