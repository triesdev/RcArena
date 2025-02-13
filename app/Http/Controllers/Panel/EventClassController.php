<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\EventClass;
use App\Models\Ticket;
use App\Models\TransactionDetailUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventClassController extends ApiController
{
    public function eventClasses($event_id, Request $request)
    {
        $data = EventClass::where("classes.event_id",$event_id)
            ->when($request->name, function ($query) use ($request) {
                $query->where('classes.name', 'like', '%' . $request->name . '%');
            })
            ->select("classes.*")
            ->leftJoin("tickets", "classes.id", "=", "tickets.class_id")
            ->leftJoin("transaction_details", "tickets.id", "=", "transaction_details.ticket_id")
            ->leftJoin("transaction_detail_users", "transaction_details.id", "=", "transaction_detail_users.transaction_detail_id")
            ->groupBy("classes.id")
            ->select("classes.*", DB::raw("count(distinct transaction_detail_users.id) as total_participants"),DB::raw("count(distinct tickets.id) as total_tickets"))
            ->paginate($request->per_page ?? 10);

        return $this->successResponse("Success", $data);
    }

    public function classParticipants($id, Request $request)
    {
        $event_class_id = $id;

        $data = EventClass::find($event_class_id)->participants()
            ->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->paginate($request->per_page ?? 10);

        $this->successResponse("Success", $data);
    }

    public function eventClassVariants($class_id)
    {
        $tickets = TransactionDetailUser::leftJoin("tickets","tickets.id","=","transaction_detail_users.ticket_id")
            ->select("tickets.*",DB::raw("count(transaction_detail_users.id) as total_participants"))
            ->where("transaction_detail_users.class_id",$class_id)
            ->groupBy("tickets.id")
            ->get();
        return $this->successResponse("Success", $tickets);
    }
}
