<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Http\Repository\TransactionRepository;
use App\Models\Role;
use App\Models\TransactionDetailUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TicketUserController extends ApiController
{
    public function userTickets(Request $request)
    {
        $transactions = (new TransactionRepository())->TicketTransactionDetailUsers($request);
        return $this->successResponse("Success", $transactions);
    }

    public function userTicketsByTransactionId($transaction_id, Request $request){
        $auth_user = $request->auth_user;
        $transactions = (new TransactionRepository())->getTicketsByTransactionId($transaction_id, $auth_user);
        return $this->successResponse("Success", $transactions);
    }

    public function transferTicket(Request $request)
    {
        /*
         * transaction_detail_users_id
         * user code
         * */

        // CHECK ROLE
        $role = Role::find($request->auth_user->role_id);
        if ($role->name != "Coordinator") {
            return $this->errorResponse("You don't have permission to update participant name");
        }

        $validator = Validator::make($request->all(), [
            'transaction_detail_users_id' => 'required',
            'user_code' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $transaction_detail_users_id = $request->input("transaction_detail_users_id");
        $user_code = $request->input("user_code");

        $transaction_detail_user = TransactionDetailUser::whereIsTransfered(0)->find($transaction_detail_users_id);
        if (!$transaction_detail_user) {
            return $this->errorResponse("Data not found");
        }

        $user = User::whereUserCode($user_code)->first();

        if (!$user) {
            return $this->errorResponse("User not found");
        }

        // CHECK IF USER TRANSFER ON OWN ID
        if ($transaction_detail_user->user_id == $user->id) {
            return $this->errorResponse("You can't transfer to your own account");
        }

        DB::beginTransaction();
        try {
            // CLONE AND CREATE NEW TRANSACTION DETAIL NEW USER
            $new_transaction_detail_user = $transaction_detail_user->replicate();
            $new_transaction_detail_user->user_id = $user->id;
            $new_transaction_detail_user->ticket_user_type = "regular"; // AUTO REGULAR
            $new_transaction_detail_user->participant_name = null;
            $new_transaction_detail_user->save();

            // UPDATE EXISTING TRANSACTION DETAIL USER IS TRANSFERED STATUS
            $transaction_detail_user->is_transfered = 1;
            $transaction_detail_user->save();

            DB::commit();
        } catch (\Exception $exception){
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse("Success", $new_transaction_detail_user);
    }

    public function updateParticipantName($transaction_detail_users_id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'participant_name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $transaction_detail_user = TransactionDetailUser::whereUserId(request()->auth_user->id)->whereIsTransfered(0)->find($transaction_detail_users_id);

        if (!$transaction_detail_user) {
            return $this->errorResponse("Data not found");
        }

        $transaction_detail_user->participant_name = request()->input("participant_name");
        $transaction_detail_user->save();

        return $this->successResponse("Success", $transaction_detail_user);
    }

    public function detailTicketUser($transaction_detail_users_id)
    {
        $transaction_detail_user = TransactionDetailUser::where("transaction_detail_users.user_id",request()->auth_user->id)
            ->whereIsTransfered(0)
            ->leftJoin("transactions", "transaction_detail_users.transaction_id", "=", "transactions.id")
            ->leftJoin("events", "transactions.event_id", "=", "events.id")
            ->leftJoin("transaction_details", "transaction_detail_users.transaction_detail_id", "=", "transaction_details.id")
            ->leftJoin("tickets", "transaction_details.ticket_id", "=", "tickets.id")
            ->leftJoin("classes", "tickets.class_id", "=", "classes.id")
            ->select(
                "transaction_detail_users.id as transaction_detail_users_id",
                "events.name as event_name",
                "events.event_date",
                "events.event_start as event_start_date",
                "events.event_end as event_end_date",
                "events.location_name as event_location_name",
                "events.location_address as event_location_address",
                "classes.name as class_name",
                "tickets.name as ticket_name",
                "transaction_detail_users.ticket_user_type",
                "transaction_detail_users.ticket_number",
            )
            ->find($transaction_detail_users_id);

        if (!$transaction_detail_user) {
            return $this->errorResponse("Data not found");
        }

        return $this->successResponse("Success", $transaction_detail_user);
    }
}
