<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\ClassModel;
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

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id'   => 'required',
            'event_name' => 'required',
            'name'       => 'required',
            'reward'     => 'required',
            'price'     => 'required',
            'quota'     => 'required',
            'is_active'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors());
        }

        ClassModel::create($request->all());

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
            return $this->errorResponse($validator->errors()->first(), $validator->errors());
        }

        $class = ClassModel::find($class_id);

        if ($class) {
            $class->update($request->all());
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
