<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\Ticket;
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

        $tickets = Ticket::when($request->name, function ($q){
            $q->where('tickets.name', 'like', '%' . $request->name . '%');
        })
            ->leftJoin('classes', 'tickets.class_id', '=', 'classes.id')
            ->where("tickets.event_id",$request->event_id)
            ->select("tickets.id","tickets.name","classes.name as class_name","tickets.price")
            ->get();

        return $this->successResponse("Success", $tickets);
    }
}
