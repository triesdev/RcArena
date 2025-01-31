<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Repository\TransactionRepository;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionDetailUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with('user')
            ->leftJoin('events', 'transactions.event_id', '=', 'events.id')
            ->leftJoin('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->orderBy('transactions.created_at', 'desc')
            ->select('transactions.*', 'events.name as event_name',DB::raw('SUM(transaction_details.qty) as total_qty'))
            ->groupBy('transactions.id')
            ->paginate(10);

        return $this->successResponse('Transaction list', $transactions);
    }

    public function paymentProcess($payment_id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'confirm_type' => 'required|in:confirmed,reject,pending',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        /*Get Payment Find Id*/
        $payment = Payment::find($payment_id);

        /*Validation*/
        if (!$payment) {
            return $this->errorResponse('Payment not found');
        }

        if ($payment->is_confirmed == 1) {
            return $this->errorResponse('Payment already confirmed');
        }

        if ($payment->payment_status == 'reject') {
            return $this->errorResponse('Payment already rejected');
        }
        /*End Validation*/

        $confirm_type = $request->confirm_type;
        $user = $request->auth_user;

        DB::beginTransaction();

        try {

            $is_confirmed = 0;
            if ($confirm_type == 'confirmed'){
                $is_confirmed = 1;
            }

            $payment->update([
                'confirmation_by_user_id' => $user->id,
                'is_confirmed' => $is_confirmed,
                'payment_status' => $confirm_type,
                'note' => $request->note,
            ]);

            $transaction_id = $payment->transaction_id;

            $transaction_status = "process";
            if ($confirm_type == 'confirmed'){
                // Generate Ticket
                $this->generateTicketTransactionDetailUsers($transaction_id);
                $transaction_status = "success";
            }

            if ($confirm_type == 'reject'){
                // Rollback Stock
                $this->rollbackStock($transaction_id);
                $transaction_status = "reject";
            }

            if($confirm_type == 'pending'){
                // Do nothing
            }

            $transaction = Transaction::find($transaction_id);
            $transaction->update([
                'transaction_status' => $transaction_status,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }

        return $this->successResponse('Payment Process', $payment);
    }

    private function rollbackStock($transaction_id)
    {
        $transaction_details = TransactionDetail::whereTransactionId($transaction_id)->orderBy("id")->get();
        $ticket_ids = $transaction_details->pluck('ticket_id')->toArray();
        // Lock Ticket
        $tickets = Ticket::whereIn('id', $ticket_ids)->lockForUpdate()->get();
        foreach ($transaction_details as $detail){
            $ticket = $tickets->where('id', $detail->ticket_id)->first();
            $ticket->quota_left = $ticket->quota_left + $detail->qty;
            $ticket->save();
        }
    }

    private function generateTicketTransactionDetailUsers($transaction_id)
    {
        // Transaction Details Users

        $transaction_details = TransactionDetail::whereTransactionId($transaction_id)->get();

        foreach ($transaction_details as $detail){
            // Loop Base On Qty
            for ($i=0; $i < $detail->qty; $i++) {
                $ticket_number = "TX".rand(100000,999999);
                $transaction_detail_user = new TransactionDetailUser();
                $transaction_detail_user->user_id = $detail->user_id;
                $transaction_detail_user->transaction_detail_id = $detail->id;
                $transaction_detail_user->transaction_id = $detail->transaction_id;
                $transaction_detail_user->qty = 1;
                $transaction_detail_user->ticket_number = $ticket_number;
                $transaction_detail_user->save();
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $repo = new TransactionRepository();
        $transaction = $repo->getDetailTransactionById($id, true);

        if (!$transaction) {
            return $this->errorResponse("Data not found");
        }
        return $this->successResponse("Success", $transaction);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
