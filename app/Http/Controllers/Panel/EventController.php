<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends PanelController
{
    public function index(Request $request)
    {
        $data = Event::when($request->name, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        })
            ->paginate($request->per_page ?? 10);

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_organizer_id" => "required",
            "name" => "required",
            "image_uri" => "required",
            "description" => "required",
            "event_launch_at" => "required",
            "ticket_purchasing_at" => "required",
            "location_name" => "required",
            "location_address" => "required",
            "event_date" => "required",
            "event_start" => "required",
            "event_end" => "required",
            "schedules" => "required",
            "is_active" => "required",
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = Event::create([
            "user_organizer_id" => $request->user_organizer_id,
            "created_by_user_id" => $request->auth_user['id'],
            "name" => $request->name,
            "image_uri" => $request->image_uri,
            "description" => $request->description,
            "event_launch_at" => $request->event_launch_at,
            "ticket_purchasing_at" => $request->ticket_purchasing_at,
            "location_name" => $request->location_name,
            "location_address" => $request->location_address,
            "event_date" => $request->event_date,
            "event_start" => $request->event_start,
            "event_end" => $request->event_end,
            "schedules" => $request->schedules,
            "is_active" => $request->is_active,
        ]);

        return $this->successResponse("Success", $data);
    }

    public function show($id)
    {
        $data = Event::find($id);
        return $this->successResponse("Success", $data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "user_organizer_id" => "required",
            "name" => "required",
            "image_uri" => "required",
            "description" => "required",
            "event_launch_at" => "required",
            "ticket_purchasing_at" => "required",
            "location_name" => "required",
            "location_address" => "required",
            "event_date" => "required",
            "event_start" => "required",
            "event_end" => "required",
            "schedules" => "required",
            "is_active" => "required",
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = Event::find($id);
        $data->update([
            "user_organizer_id" => $request->user_organizer_id,
            "name" => $request->name,
            "image_uri" => $request->image_uri,
            "description" => $request->description,
            "event_launch_at" => $request->event_launch_at,
            "ticket_purchasing_at" => $request->ticket_purchasing_at,
            "location_name" => $request->location_name,
            "location_address" => $request->location_address,
            "event_date" => $request->event_date,
            "event_start" => $request->event_start,
            "event_end" => $request->event_end,
            "schedules" => $request->schedules,
            "is_active" => $request->is_active,
        ]);
        return $this->successResponse("Success", $data);
    }

    public function destroy($id)
    {
        $data = Event::find($id)->delete();
        return $this->successResponse("Success", $data);
    }
}
