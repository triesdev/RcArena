<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Role;
use App\Models\User;
use App\Utils\StringGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class AuthController extends ApiController
{

    public function Auth(Request $request)
    {
        return $this->successResponse("Successs", $request->all());
    }

    public function Login(Request $request)
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
                'api_token' => $new_token
            ]);

        return $this->successResponse("Success", [
            "user" => $user,
            "token" => $new_token
        ]);
    }

    public function Register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return $this->successResponse("Success", $user);
    }

    public function Logout(Request $request)
    {
        $user = User::whereEmail($request->auth_user['email'])->first();

        $user->update([
            'api_token' => null,
            'fcm_token' => null,
        ]);

        return $this->successResponse();
    }

    public function SSOLogin(Request $request)
    {
        try {

            Log::info("SSO Login Request: " . json_encode($request->all()));

            $client = new Client();
            $endPoint = "https://www.googleapis.com/oauth2/v3/userinfo?access_token=" . $request->access_token;
            $response = $client->get($endPoint, [
                'headers' => [
                    'content-type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ])->getBody()->getContents();

            if(!$response){
                return $this->errorResponse("Failed to get user data");
            }

            $userInfo = json_decode($response, true);
            $image_uri = $userInfo['picture'] ?? null;
            $email = $userInfo['email'] ?? null;
            $name = $userInfo['name'] ?? null;

            /*Create Or Update Users Type Mobile*/
            $user = User::whereEmail($email)->first();
            if (!$user) {
                $user = User::create([
                    'user_code' => StringGenerator::generateUserCode(),
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(rand(100000, 999999)),
                    'phone_number' => '',
                    'user_type_mobile' => 'regular',
                    'image_uri' => $image_uri,
                    'api_token' => StringGenerator::generateAlphanumeric(60),
                    'role_id' => Role::whereIsDefault(1)->whereType('mobile')->first()->id,
                ]);
            } else {
                $user->update([
                    'name' => $name,
                    'image_uri' => $image_uri,
                    'api_token' => StringGenerator::generateAlphanumeric(60)
                ]);
            }


            return $this->successResponse("Success", [
                "user" => $user,
                "token" => $user->api_token
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function RegisterFcmToken(Request $request)
    {
        $user = User::whereEmail($request->auth_user['email'])->first();

        $user->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return $this->successResponse();
    }

    public function loginApple(Request $request)
    {
        $validator = Validator::make([
            'id_token'    => 'required',
        ],[
            'id_token.required'    => 'Token tidak boleh kosong',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse();
        }

        $client = new Client();
        $endpoint = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithIdp?key=" . env('FB_API_KEY');
        try {
            $response = $client->request(
                'POST',
                $endpoint,
                [
                    'form_params' => [
                        "postBody"            => "id_token=".$request->id_token . "&providerId=apple.com",
                        "requestUri"          => "http://localhost",
                        "returnIdpCredential" => true,
                        "returnSecureToken"   => true
                    ]
                ]
            );

            $dataApple =  json_decode((string)$response->getBody(), true);

            $email = $dataApple['email'] ?? null;
            $password = Hash::make(rand(100000, 999999));
            $image_uri = $userInfo['picture'] ?? null;
            $name = explode('@', $email)[0];

            /*Create Or Update Users Type Mobile*/
            $user = User::whereEmail($email)->first();
            if (!$user) {
                $user = User::create([
                    'user_code' => StringGenerator::generateUserCode(),
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'phone_number' => '',
                    'user_type_mobile' => 'regular',
                    'image_uri' => $image_uri,
                    'api_token' => StringGenerator::generateAlphanumeric(60),
                    'role_id' => Role::whereIsDefault(1)->whereType('mobile')->first()->id,
                ]);
            } else {
                $user->update([
                    'name' => $name,
                    'image_uri' => $image_uri,
                    'api_token' => StringGenerator::generateAlphanumeric(60)
                ]);
            }

            $token_login = StringGenerator::generateAlphanumeric(60);

        } catch (\Exception $exception) {
            Log::error("Apple login error: " . $exception->getMessage());
            return $this->errorResponse("Upss... terjadi kesalahan apple :(", $exception->getMessage());
        }

        return $this->successResponse("Success", [
            "user" => $dataApple,
            "token" => $token_login
        ]);
    }
}
