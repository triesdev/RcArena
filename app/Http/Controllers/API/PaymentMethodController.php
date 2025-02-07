<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends ApiController
{
    public function index(Request $request)
    {
        $data = PaymentMethod::select("id","name","code","account_name","account_number","type","image_uri")->get();
        return $this->successResponse("Success", $data);
    }
}
