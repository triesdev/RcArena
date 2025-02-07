<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Repository\TransactionRepository;
use App\Models\EventClass;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketBundle;
use App\Models\TransactionDetailUser;
use App\Models\User;
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
            $classes = EventClass::select("id as class_id","name as class_name","price")->whereEventId($id)->get();

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
}
