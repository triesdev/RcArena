<?php

namespace App\Http\Controllers\Panel;

class FirebaseController extends PanelController
{
    public function loadConfig()
    {
        $config = [
            "apiKey" => env("FB_API_KEY"),
            "authDomain" => env("FB_AUTH_DOMAIN"),
            "databaseURL" => env("FB_DATABASE_URL"),
            "projectId" => env("FB_PROJECT_ID"),
            "storageBucket" => env("FB_STORAGE_BUCKET"),
            "messagingSenderId" => env("FB_MESSAGING_SENDER_ID"),
            "appId" => env("FB_APP_ID"),
            "measurementId" => env("FB_MEASUREMENT_ID"),
        ];

        return $this->successResponse("Success", $config);
    }
}
