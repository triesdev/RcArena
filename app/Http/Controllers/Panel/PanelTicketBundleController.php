<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Models\TicketBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanelTicketBundleController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "class_id" => "required",
            "event_id" => "required",
            "ticket_bundle_id" => "required",
            "name" => "required",
            "ticket_type" => "required",
            "price" => "required",
            "quota_left" => "required",
            "quota" => "required",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors());
        }

        TicketBundle::create($request->all());

        return $this->createSuccessResponse();
    }

    public function update($class_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            "class_id" => "required",
            "event_id" => "required",
            "ticket_bundle_id" => "required",
            "name" => "required",
            "ticket_type" => "required",
            "price" => "required",
            "quota_left" => "required",
            "quota" => "required",
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors());
        }

        $class = TicketBundle::find($class_id);

        if($class){
            $class->update($request->all());
            return $this->successResponse();
        }

        return $this->notFoundResponse();
    }

    public function destroy($id)
    {
        $class = TicketBundle::find($id);

        if($class){
            $class->delete();
            return $this->successResponse();
        }

        return $this->notFoundResponse();
    }
}
