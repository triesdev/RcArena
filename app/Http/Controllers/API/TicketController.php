<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketBundle;

class TicketController extends ApiController
{
    public function getTicketByEventId($id)
    {
        try {
            $ticket_bundles = TicketBundle::whereEventId($id)->get();
            $ticket_bundle_ids = $ticket_bundles->pluck('id');
            $ticket_by_bundle_ids = Ticket::whereIn('ticket_bundle_id', $ticket_bundle_ids)->get();

            // Get Ticket Bundles
            $ticket_bundles = $ticket_bundles->map(function ($ticket_bundle) use ($ticket_by_bundle_ids) {
                $ticket_bundle->tickets = $ticket_by_bundle_ids->where('ticket_bundle_id', $ticket_bundle->id);
                return $ticket_bundle;
            });

            // Get Ticket per Picecs
            $ticket_pieces = Ticket::whereNull("ticket_bundle_id")->get();

            $this->successResponse("Success", [
                "ticket_bundles" => $ticket_bundles,
                "ticket_pieces" => $ticket_pieces
            ]);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }
}
