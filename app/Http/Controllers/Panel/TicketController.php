<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\Ticket;
use App\Models\TicketBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends ApiController
{
    public function properties(Request $request)
    {
        // Validate Event Id
        $validation = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id'
        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->errors()->first(), $validation->errors());
        }

        $ticket_pieces = Ticket::when($request->name, function ($q){
            $q->where('tickets.name', 'like', '%' . $request->name . '%');
        })
            ->leftJoin('classes', 'tickets.class_id', '=', 'classes.id')
            ->where("tickets.event_id",$request->event_id)
            ->select("tickets.id","tickets.name","classes.name as class_name","tickets.price")
            ->whereNull('tickets.ticket_bundle_id')
            ->get();

        $ticket_bundles = TicketBundle::when($request->name, function ($q){
            $q->where('ticket_bundles.name', 'like', '%' . $request->name . '%');
            })->with("tickets", function ($q){
                $q->leftJoin('classes', 'tickets.class_id', '=', 'classes.id')
                ->leftJoin('events', 'tickets.event_id', '=', 'events.id')
                ->select("tickets.id","tickets.ticket_bundle_id","tickets.name as ticket_name","events.name as event_name","classes.name as class_name","tickets.price");
            })
            ->where("event_id",$request->event_id)
            ->select("id","name as ticket_bundle_name","price as ticket_bundle_price")
            ->get();

        return $this->successResponse("Success", [
            "ticket_pieces" => $ticket_pieces,
            "ticket_bundles" => $ticket_bundles,
            "tickets" => []
        ]);
    }
}
