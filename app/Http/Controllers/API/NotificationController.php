<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request)
    {
        if($request->category === 'notification'){
            $data = Notification::where('category', $request->category)
                ->where('user_id', $request->auth_user['id'])
                ->get();
        } else {
            $data = Notification::where('category', $request->category)
                ->get();
        }
        return $this->successResponse('Success', $data);
    }
}
