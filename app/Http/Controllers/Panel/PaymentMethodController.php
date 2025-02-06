<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends ApiController
{
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $per_page = $request->per_page ?? 25;
        $data = PaymentMethod::when($request->name, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        })
        ->paginate($per_page, ['*'], 'page', $page);

        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "code" => "required",
            "account_name" => "required",
            "account_number" => "required",
            "type" => "required",
            "image_uri" => "required",
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $payment_method = PaymentMethod::create([
            "name" => $request->name,
            "code" => $request->code,
            "account_name" => $request->account_name,
            "account_number" => $request->account_number,
            "type" => $request->type,
            "image_uri" => $request->image_uri,
        ]);

        return $this->successResponse("Success", $payment_method);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "code" => "required",
            "account_name" => "required",
            "account_number" => "required",
            "type" => "required",
            "image_uri" => "required",
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $payment_method = PaymentMethod::find($id);

        if (!$payment_method) {
            return $this->errorResponse("Data not found");
        }

        $payment_method->update([
            "name" => $request->name,
            "code" => $request->code,
            "account_name" => $request->account_name,
            "account_number" => $request->account_number,
            "type" => $request->type,
            "image_uri" => $request->image_uri,
        ]);

        return $this->successResponse("Success", $payment_method);
    }

    public function destroy($id)
    {
        $payment_method = PaymentMethod::find($id);

        if (!$payment_method) {
            return $this->errorResponse("Data not found");
        }

        $payment_method->delete();

        return $this->successResponse("Success", $payment_method);
    }

    public function show($id)
    {
        $data = PaymentMethod::find($id);
        return $this->successResponse("Success", $data);
    }
}
