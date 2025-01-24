<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'name' => 'required',
            'account_name' => 'required',
            'phone_number' => 'required',
            'payment_proof_image_uri' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Errors", $validator->errors());
        }

        $transaction = Transaction::find($request->transaction_id);
        if (!$transaction) {
            return $this->errorResponse("Transaction not found");
        }

        if ($transaction->transaction_status != 'unpaid'){
            return $this->errorResponse("Transaction already paid");
        }

        DB::beginTransaction();
        try {
            $transaction->update([
                'transaction_status' => 'process'
            ]);

            $auth_user = $request->auth_user;

            $payment = Payment::create([
                'user_id' => $auth_user->id,
                'transaction_id' => $request->transaction_id,
                'name' => $request->name,
                'payment_date' => date('Y-m-d H:i:s'),
                'account_name' => $request->account_name,
                'phone_number' => $request->phone_number,
                'payment_proof_image_uri' => $request->payment_proof_image_uri,
                'nominal_payment' => $transaction->total_price,
                'is_confirmed' => 0,
            ]);

            DB::commit();
            return $this->successResponse("Success", $payment);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->errorResponse($ex->getMessage());
        }
    }
}
