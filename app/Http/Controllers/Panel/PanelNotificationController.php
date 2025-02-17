<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanelNotificationController extends ApiController
{
    public function index(Request $request)
    {
        $data = Notification::where('category', 'information')
            ->when($request->title, function ($query) use ($request) {
                return $query->where('title', 'like', '%' . $request->title . '%');
            })
            ->paginate(10);

        return $this->successResponse("Success", $data);
    }

    public function show($id)
    {
        $data = Notification::find($id);

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors());
        }

        $nc = new NotificationController();
        $nc->sendNotificationTopic($request);

        return $this->successResponse();
    }

    // public function update($id, Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'required',
    //         'message' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->errorResponse($validator->errors());
    //     }

    //     $nc = new NotificationController();
    //     $nc->sendNotificationTopic($request);

    //     return $this->successResponse();
    // }

    public function destroy($id)
    {
        $notification = Notification::find($id);
        $notification->delete();

        return $this->successResponse();
    }
}
