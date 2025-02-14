<?php

namespace App\Http\Controllers\Panel;

use App\Http\Traits\FCM;
use App\Models\Notification;
use App\Models\User;
use App\Utils\StringGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PanelAuthController extends PanelController
{
    use FCM;
    public function loginView()
    {
        return view('admin.Login');
    }

    public function adminPanel()
    {
        return view('admin.Layout');
    }

    public function notFound()
    {
        return view('404');
    }
    public function authView()
    {
        return view('admin.auth');
    }

    public function auth(Request $request)
    {
        return $this->successResponse("Successs", $request->all());
    }

    public function login(Request $request)
    {
        $new_token = StringGenerator::generateAlphanumeric(60);

        $validator = Validator::make([
            'email'    => 'required',
            'password' => 'required',
        ], [
            'email.required'    => 'Email atau password tidak sesuai.',
            'password.required' => 'Email atau password tidak sesuai.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse();
        }

        $user = User::whereEmail($request->email)->first();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->unauthorizedResponse();
        }

        User::whereEmail($request->email)
            ->first()
            ->update([
                'panel_token' => $new_token
            ]);

        return $this->successResponse("Success", [
            "user" => $user,
            "token" => $new_token
        ]);
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->successResponse("Success", $user);
    }

    public function logout(Request $request)
    {
        $user = User::whereEmail($request->auth_user['email'])->first();

        $user->update([
            'api_token' => null
        ]);

        return $this->successResponse("Success");
    }

    public function testSendNotification(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'fcm_token' => 'required',
           'title' => 'required',
           'message' => 'required',
        ]);

        if($validator->fails()){
            return $this->errorResponse($validator->errors()->first());
        }

        $user = User::where('fcm_token', $request->fcm_token)
            ->first();

        $notification = Notification::create([
            "user_id" => $user ? $user->id : null,
            "category" => "information",
            "label" => "general_info",
            "flag" => "info",
            "title" => $request->title,
            "message" => $request->message,
            "page_route" => $request->page_route,
            "reference_id" => $request->reference_id,
            "status" => 1,
        ]);

        try{
            $send = $this->sendNotification($request->fcm_token, [
                'title'=> $request->title,
                'message' => $request->message,
                "page_route"=> $request->page_route,
                "reference_id" => $request->reference_id
            ]);

            return $this->successResponse("success",$send);
        }catch (\Exception $e){
            $notification->update([
               'status'=>2
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function sendNotificationTopic(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'title' => 'required',
           'message' => 'required',
        ]);

        if($validator->fails()){
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

        try{
            $send = $this->sendNotifByTopic("general_info", [
                'title'=> $request->title,
                'message' => $request->message,
                "page_route"=> $request->page_route,
                "reference_id" => $request->reference_id
            ]);

            return $this->successResponse("Notif by topic", $send);
        } catch (\Exception $e){
            $notification->update([
               'status' => 2
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }
}
