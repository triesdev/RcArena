<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class PanelNotificationController extends ApiController
{
    public function index()
    {
        $data = Notification::paginate(10);

        return $this->successResponse("Success", $data);
    }

    public function store() {}
}
