<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\TransactionDetailUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TransactionDetailUsersController extends ApiController
{
    public function updateParticipantData($transaction_detail_user_id, Request $request)
    {
        /*
         * payload:
         *      participant_name
         *      participant_chair
         * end payload
         *
         * is_locked = 1 (Default if update to lock the table list)
         * */

        $validation = Validator::make($request->all(), [
            'participant_name' => 'required',
            'participant_chair_number' => 'required',
            'password' => 'required_if:edit,true'
        ],[
            'participant_name.required' => 'Participant Name is required',
            'participant_chair_number.required' => 'Participant Chair Number is required',
            'password.required_if' => 'Password is required'
        ]);

        if ($validation->fails()) {
            return $this->errorResponse($validation->errors()->first(), $validation->errors());
        }

        // Validation Password User
        if ($request->edit) {
            if (!password_verify($request->password, $request->auth_user->password)) {
                return $this->errorResponse("Password is incorrect", null, 401);
            }
        }

        $transaction_user = TransactionDetailUser::find($transaction_detail_user_id);

        if ($transaction_user) {
            $transaction_user->update(
                [
                    'participant_name' => $request->participant_name,
                    'participant_chair_number' => $request->participant_chair_number,
                    'is_locked' => 1,
                    'is_locked_by' => $request->auth_user->id,
                    'is_locked_at' => now()
                ]
            );
            return $this->successResponse("Success", $transaction_user);
        }

        return $this->notFoundResponse();
    }
}
