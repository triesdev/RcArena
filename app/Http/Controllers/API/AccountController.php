<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends ApiController
{
    public function Index(Request $request)
    {
        $data = Account::when($request->category, function ($query) use ($request) {
            $query->where('category', $request->category);
        })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            });

        if ($request->pagination == true) {
            $data = $data->paginate($request->per_page ?? 10);
        } else {
            $data = $data->get();
        }

        return $this->successResponse("Success", $data);
    }

    public function Store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|in:asset,liability,equity,revenue,expense',
            'type' => 'required|in:code,subcode',
            'name' => 'required',
            'code' => 'required',
            'subcode' => 'required_if:type,subcode',
            'post_saldo' => 'required|in:debit,credit',
            'post_report' => 'required|in:balance,profit_loss',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $code = Account::where('code', $request->code)->first();

        if (!$code && $request->type == 'subcode') {
            return $this->errorResponse("Code not exist",);
        }

        if ($code && $request->type == 'code') {
            return $this->errorResponse("Code already exist",);
        }

        $data = Account::create($request->all());
        return $this->successResponse("Creates", $data);
    }

    public function Update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|in:asset,liability,equity,revenue,expense',
            'type' => 'required|in:code,subcode',
            'name' => 'required',
            'code' => 'required',
            'subcode' => 'required_if:type,subcode',
            'post_saldo' => 'required|in:debit,credit',
            'post_report' => 'required|in:balance,profit_loss',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first(), $validator->errors());
        }

        $data = Account::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $data->update([
            'category' => $request->category,
            'name' => $request->name,
            'post_saldo' => $request->post_saldo,
            'post_report' => $request->post_report,
        ]);
        return $this->successResponse("Updated", $data);
    }

    public function Destroy($id)
    {
        $data = Account::find($id);

        if (!$data) {
            return $this->notFoundResponse();
        }

        $data->delete();
        return $this->successResponse("Deleted");
    }
}
