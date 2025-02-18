<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Traits\FCM;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends ApiController
{
    use FCM;
    public function index(Request $request)
    {
        if ($request->category === 'notification') {
            $data = Notification::where('category', $request->category)
                ->where('user_id', $request->auth_user['id'])
                ->orderByDesc('created_at')
                ->offset($request->offset ?? 0)
                ->limit($request->limit ?? 10)
                ->get();
        } else {
            $data = Notification::where('category', $request->category)
                ->orderByDesc('created_at')
                ->offset($request->offset ?? 0)
                ->limit($request->limit ?? 10)
                ->get();
        }
        return $this->successResponse('Success', $data);
    }

    public function notificationCount(Request $request)
    {
        $unread = Notification::where('user_id', $request->auth_user['id'])
            ->whereIsRead(0)
            ->count();

        return $this->successResponse("Success", [
            "count" => $unread
        ]);
    }

    public function readNotification($id)
    {
        $notification = Notification::whereIsRead(0)->find($id);

        if ($notification) {
            $notification->update([
                'is_read' => 1
            ]);
        }

        return $this->successResponse();
    }

    public function testSendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required',
            'title' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $user = User::where('fcm_token', $request->fcm_token)
            ->first();

        $notification = Notification::create([
            "user_id" => $user ? $user->id : null,
            "category" => "notification",
            "label" => "general_info",
            "flag" => "info",
            "title" => $request->title,
            "message" => $request->message,
            "page_route" => $request->page_route,
            "reference_id" => $request->reference_id,
            "status" => 1,
        ]);

        try {
            $send = $this->sendNotification($request->fcm_token, [
                'title' => $request->title,
                'message' => $request->message,
                "page_route" => $request->page_route,
                "reference_id" => $request->reference_id
            ]);

            return $this->successResponse("success", $send);
        } catch (\Exception $e) {
            $notification->update([
                'status' => 2
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function sendNotificationTopic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $notification = Notification::create([
            "category" => "information",
            "label" => "general_info",
            "flag" => "info",
            "title" => $request->title,
            "message" => $request->message,
            "page_route" => $request->page_route,
            "reference_id" => $request->reference_id,
            "status" => 1,
        ]);

        try {
            $send = $this->sendNotifByTopic("general_info", [
                'title' => $request->title,
                'message' => $request->message,
                "page_route" => $request->page_route,
                "reference_id" => $request->reference_id
            ]);

            return $this->successResponse("Notif by topic", $send);
        } catch (\Exception $e) {
            $notification->update([
                'status' => 2
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }
}
