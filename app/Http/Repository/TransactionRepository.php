<?php

namespace App\Http\Repository;
use App\Http\Controllers\ApiController;
use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function TicketTransactionDetailUsers(Request $request){
        $user = $request->auth_user;
        $status = $request->input("status");

        $sqlRawInject = "";
        $nowDateTime = date('Y-m-d H:i:s');
        if ($status == 'active'){
            // EVENT ACTIVE BY DATE
            $sqlRawInject = " AND events.event_end >= '{$nowDateTime}'";
            $sqlRawInject .= " AND events.event_start <= '{$nowDateTime}'";
        } else if ($status == 'inactive'){
            // EVENT PAST BY DATE
            $sqlRawInject = " AND events.event_start < '{$nowDateTime}'";
        }

        $rawQuery = "
            SELECT
                transactions.id as transaction_id,
                transactions.transaction_number,
                transactions.transaction_date,
                transaction_details.user_id,
                COUNT(transaction_details.id) as total_ticket,
                transactions.event_id as event_id, events.name as event_name, events.event_date, events.event_start as event_start_date, events.event_end as event_end_date, events.location_name as event_location_name, events.location_address as event_location_address
            FROM transactions
            LEFT JOIN events ON transactions.event_id = events.id
            LEFT JOIN (
                SELECT transaction_details.id, transaction_details.transaction_id, transaction_detail_users.id as transaction_detail_users_id, transaction_detail_users.user_id FROM transaction_details JOIN transaction_detail_users ON transaction_details.id = transaction_detail_users.transaction_detail_id WHERE transaction_detail_users.user_id = {$user->id}
                 AND transaction_detail_users.is_transfered = 0 AND transaction_detail_users.deleted_at IS NULL
            ) as transaction_details ON transactions.id = transaction_details.transaction_id
            WHERE transactions.transaction_status = 'success' AND transactions.deleted_at IS NULL
            AND transaction_details.user_id = {$user->id}
            {$sqlRawInject}
            GROUP BY transactions.id
            HAVING total_ticket > 0
        ";

        $transaction = DB::select($rawQuery);

        return $transaction;
    }

    public function getTicketsByTransactionId($transaction_id, $auth_user)
    {
        $user_id = $auth_user->id;
        return $transactions = Transaction::with(["transaction_detail_users" => function ($q) use ($user_id) {
            $q->leftJoin("transaction_details", "transaction_detail_users.transaction_detail_id", "=", "transaction_details.id")
                ->leftJoin("tickets", "transaction_details.ticket_id", "=", "tickets.id")
                ->leftJoin("classes", "tickets.class_id", "=", "classes.id")
                ->leftJoin("users", "transaction_detail_users.user_id", "=", "users.id")
                ->select(
                    "transaction_detail_users.id as transaction_detail_users_id",
                    "transaction_details.transaction_id",
                    "transaction_detail_users.ticket_user_type",
                    "tickets.name as ticket_name",
                    "classes.name as class_name",
                    "users.user_code",
                    "users.name as user_name",
                    "transaction_detail_users.ticket_number",
                    "transaction_detail_users.participant_name",
                    "transaction_detail_users.participant_chair_number",
                    "transaction_detail_users.is_transfered"
                )->where("transaction_detail_users.user_id","=",$user_id);
        }])
        ->whereHas("transaction_detail_users", function ($q) use ($user_id) {
            $q->where("transaction_detail_users.user_id","=",$user_id);
        })
        ->leftJoin("events", "transactions.event_id", "=", "events.id")
        ->select(
    "transactions.id",
            "transactions.transaction_number",
            "transactions.transaction_date",
            "transactions.transaction_type",
            "events.id  as event_id",
            "events.name as event_name",
            "events.location_name as event_location_name",
            "events.location_address as event_location_address",
            "events.event_date as event_date",
            "events.event_start as event_start_date",
            "events.event_end as event_end_date",
        )
        ->find($transaction_id);

        // IF ROLE COORDINATOR
        $role = Role::find($auth_user->role_id);

        $transactions->transaction_detail_users = $transactions->transaction_detail_users->map(function ($transaction_detail_user) use ($role) {
            if ($role->name == "Coordinator" && $transaction_detail_user->is_transfered == 0 && $transaction_detail_user->ticket_user_type == 'community') {
                $transaction_detail_user->enable_transfer = true;
            } else {
                $transaction_detail_user->enable_transfer = false;
            }
            return $transaction_detail_user;
        });

        // Mapping Transfered Ticket And Normal Ticket
        $transactions->own_tickets = $transactions->transaction_detail_users->where("is_transfered",0)->flatten();
        $transactions->transfered_tickets = $transactions->transaction_detail_users->where("is_transfered",1)->flatten();

        // Delete Transaction Detail Users
        unset($transactions->transaction_detail_users);

        return $transactions;
    }
}
