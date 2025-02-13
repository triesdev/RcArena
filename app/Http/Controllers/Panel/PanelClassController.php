<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\ClassModel;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanelClassController extends ApiController
{
    public function index(Request $request)
    {
        $data = ClassModel::when($request->event_id, function ($q) use ($request) {
            $q->where('event_id', $request->event_id);
        })->with('ticket')->get();

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id'   => 'required',
            'name'       => 'required',
            'reward'     => 'required',
            'price'     => 'required',
            'is_active'  => 'required',
        ]);

        $event = Event::find($request->event_id);

        if (!$event) {
            return $this->errorResponse('Event not found', [], 422);
        }

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors(), 422);
        }

        ClassModel::create([
            'event_id'   => $request->event_id,
            'event_name'   => $event->name,
            'name'       => $request->name,
            'reward'     => $request->reward,
            'price'     => $request->price,
            'is_active'  => $request->is_active
        ]);

        return $this->createSuccessResponse();
    }

    public function update($class_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'reward'     => 'required',
            'is_active'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors(), 422);
        }

        $class = ClassModel::find($class_id);

        if ($class) {
            $class->update([
                'name'       => $request->name,
                'price'      => $request->price,
                'reward'     => $request->reward,
                'is_active'  => $request->is_active
            ]);
            return $this->successResponse();
        }

        return $this->notFoundResponse();
    }

    public function destroy($id)
    {
        $class = ClassModel::find($id);

        if ($class) {
            $class->delete();
            return $this->successResponse();
        }

        return $this->notFoundResponse();
    }
}
