<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\Ticket;
use App\Models\TicketBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PanelTicketBundleController extends ApiController
{
    public function index(Request $request)
    {
        $data = TicketBundle::when($request->event_id, function ($q) use ($request) {
            $q->where('event_id', $request->event_id);
        })->with('tickets')->get();

        return $this->successResponse("Success", $data);
    }

    public function show($id)
    {
        $data = TicketBundle::with('tickets')->find($id);

        return $this->successResponse("Success", $data);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "event_id" => "required",
            "name" => "required",
            "price" => "required",
            "tickets" => "required|array",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors(), 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $ticket_bundle = TicketBundle::create([
                    "event_id" => $request->event_id,
                    "name" => $request->name,
                    "price" => $request->price
                ]);

                foreach (collect($request->tickets) as $ticket) {
                    Ticket::where('id', $ticket['id'])->update([
                        'ticket_bundle_id' => $ticket_bundle->id
                    ]);
                }
            });
        } catch (\Throwable $th) {
            return $this->validationErrorResponse($th->getMessage());
        }


        return $this->createSuccessResponse();
    }

    public function update($ticket_bundle_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            "event_id" => "required",
            "name" => "required",
            "price" => "required",
            "tickets" => "required|array",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors(), 422);
        }

        $ticket_bundle = TicketBundle::find($ticket_bundle_id);

        if ($ticket_bundle) {
            DB::transaction(function () use ($request, $ticket_bundle) {
                $ticket_bundle->update([
                    "name" => $request->name,
                    "price" => $request->price
                ]);

                $active_ticket_ids = [];
                foreach (collect($request->tickets) as $ticket) {
                    Ticket::where('id', $ticket['id'])->update([
                        'ticket_bundle_id' => $ticket_bundle->id
                    ]);

                    $active_ticket_ids[] = $ticket['id'];
                }

                // handle unused
                Ticket::where('ticket_bundle_id', $ticket_bundle->id)->whereNotIn('id', $active_ticket_ids)->update([
                    'ticket_bundle_id' => null
                ]);
            });
            return $this->successResponse();
        } else {
            return $this->notFoundResponse();
        }
    }

    public function destroy($id)
    {
        $class = TicketBundle::find($id);

        if ($class) {
            $class->delete();
            return $this->successResponse();
        }

        return $this->notFoundResponse();
    }
}
