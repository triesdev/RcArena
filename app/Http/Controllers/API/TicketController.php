<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\EventClass;
use App\Models\Ticket;
use App\Models\TicketBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TicketController extends ApiController
{
    public function getTicketByEventId(Request $request)
    {
        try {
            $id = $request->input("event_id");

            $selectRawTicketBundles = "ticket_bundles.id as ticket_bundle_id,ticket_bundles.name,ticket_bundles.price";
            $ticket_bundles = TicketBundle::whereEventId($id)->selectRaw($selectRawTicketBundles)->get();

            $selectRawTickets = "tickets.id as ticket_id,tickets.ticket_bundle_id,ticket_type,tickets.price,tickets.quota,tickets.quota_left,tickets.name as ticket_name, classes.id as class_id, classes.name as class_name";

            $ticket_bundle_ids = $ticket_bundles->pluck('id');
            $ticket_by_bundle_ids = Ticket::leftJoinClass()->selectRaw($selectRawTickets)->get();

            // Get Ticket Bundles
            $ticket_bundles = $ticket_bundles->map(function ($ticket_bundle) use ($ticket_by_bundle_ids) {
                $ticket_bundle->tickets = $ticket_by_bundle_ids->where('ticket_bundle_id', $ticket_bundle->ticket_bundle_id)->flatten();
                return $ticket_bundle;
            });

            // Get Ticket per Pieces
            $classes = EventClass::select("id as class_id","name as class_name")->whereEventId($id)->get();

            $ticket_pieces = $ticket_by_bundle_ids->where('ticket_bundle_id', null)->flatten();

            foreach ($classes as $class) {
               $class->tickets = $ticket_pieces->where('class_id', $class->class_id)->flatten();
            }

            return $this->successResponse("Success", [
                "ticket_bundles" => $ticket_bundles,
                "ticket_pieces" => $classes
            ]);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }

    public function userTickets(Request $request)
    {
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
                transactions.id,
                transactions.transaction_number,
                transactions.transaction_date,
                COUNT(transaction_details.id) as total_ticket,
                transactions.event_id as event_id, events.name as event_name, events.event_start as event_start_date, events.event_end as event_end_date, events.location_name as event_location_name, events.location_address as event_location_address
            FROM transactions
            LEFT JOIN events ON transactions.event_id = events.id
            LEFT JOIN (
                SELECT transaction_details.id, transaction_details.transaction_id, transaction_detail_users.id as transaction_detail_users_id FROM transaction_details JOIN transaction_detail_users ON transaction_details.id = transaction_detail_users.transaction_detail_id WHERE transaction_detail_users.user_id = {$user->id} AND transaction_detail_users.deleted_at IS NULL
            ) as transaction_details ON transactions.id = transaction_details.transaction_id
            WHERE user_id = {$user->id} AND transactions.transaction_status = 'success' AND transactions.deleted_at IS NULL
            {$sqlRawInject}
            GROUP BY transactions.id
            HAVING total_ticket > 0
        ";

        $transaction = DB::select($rawQuery);

        return $this->successResponse("Success", $transaction);
    }
}
