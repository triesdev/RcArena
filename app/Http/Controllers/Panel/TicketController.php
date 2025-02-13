<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\ClassModel;
use App\Models\Ticket;
use App\Models\TicketBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends ApiController
{
    public function index(Request $request)
    {
        $data = Ticket::when($request->event_id, function ($q) use ($request) {
            $q->where('tickets.event_id', $request->event_id);
        })->where('tickets.ticket_bundle_id', null)->get();

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'class_id' => 'required',
            'event_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'quota' => 'required',
            'is_active' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->errors()->first(), $validation->errors());
        }

        $class = ClassModel::find($request->class_id);

        Ticket::create([
            "class_id" => $request->class_id,
            "class_name" => $class ? $class->name : "",
            "event_id" => $request->event_id,
            "ticket_bundle_id" => $request->ticket_bundle_id,
            "name" => $request->name,
            "ticket_type" => 'regular',
            "price" => $request->price,
            "quota_left" => $request->quota,
            "quota" => $request->quota,
            "is_active" => $request->is_active,
        ]);

        return $this->successResponse("Success");
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'class_id' => 'required',
            'event_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'quota' => 'required',
            'is_active' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->errors()->first(), $validation->errors());
        }

        $class = ClassModel::find($request->class_id);

        Ticket::where('id', $id)->update([
            "class_name" => $class ? $class->name : "",
            "name" => $request->name,
            "price" => $request->price,
            "quota" => $request->quota,
            "is_active" => $request->is_active,
        ]);

        return $this->successResponse("Success");
    }

    public function properties(Request $request)
    {
        // Validate Event Id
        $validation = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id'
        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->errors()->first(), $validation->errors());
        }

        $ticket_pieces = Ticket::when($request->name, function ($q) use ($request) {
            $q->where('tickets.name', 'like', '%' . $request->name . '%');
        })
            ->leftJoin('classes', 'tickets.class_id', '=', 'classes.id')
            ->where("tickets.event_id", $request->event_id)
            ->select("tickets.id", "tickets.class_id", "tickets.name", "classes.name as class_name", "tickets.price")
            ->whereNull('tickets.ticket_bundle_id')
            ->get();

        $ticket_bundles = TicketBundle::when($request->name, function ($q) use ($request) {
            $q->where('ticket_bundles.name', 'like', '%' . $request->name . '%');
        })->with("tickets", function ($q) {
            $q->leftJoin('classes', 'tickets.class_id', '=', 'classes.id')
                ->leftJoin('events', 'tickets.event_id', '=', 'events.id')
                ->select("tickets.id", "tickets.class_id", "tickets.ticket_bundle_id", "tickets.name as ticket_name", "events.name as event_name", "classes.name as class_name", "tickets.price");
        })
            ->where("event_id", $request->event_id)
            ->select("id", "name as ticket_bundle_name", "price as ticket_bundle_price")
            ->get();

        return $this->successResponse("Success", [
            "ticket_pieces" => $ticket_pieces,
            "ticket_bundles" => $ticket_bundles,
            "tickets" => []
        ]);
    }
}
