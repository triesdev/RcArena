<?php

namespace App\Http\Controllers\Panel;

use App\Http\Traits\FCM;
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

    public function testSendNotification()
    {
        $reg_id = 'exBZc8L6QySRNg-yxVUyse:APA91bERzz5JbGRHgWSgag4v5Bvz3Q8uhWdDulaZDA2J_YShJBwhGSl1b8mTf5cOdES48R4p4ISrcns8XrO6zWxb0dmWoh1AzDySp_e4OguHE8Sza_hZD7vFHxo5LhbPRaFpZJmUsvb8';

        return $this->sendNotification($reg_id, [
            'title'=> "Notification",
            'message' => "Notification active"
        ]);
    }
}
