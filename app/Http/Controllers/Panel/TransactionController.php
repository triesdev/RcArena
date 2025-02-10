<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Repository\TransactionRepository;
use App\Models\Event;
use App\Models\EventClass;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketBundle;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionDetailUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Util\Exception;

class TransactionController extends ApiController
{
    protected $stock_errors = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $transactions = Transaction::with('user')
//            ->leftJoin('events', 'transactions.event_id', '=', 'events.id')
            ->leftJoin('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->orderBy('transactions.created_at', 'desc')
            ->select('transactions.*',DB::raw('SUM(transaction_details.qty) as total_qty'))
            ->groupBy('transactions.id')
            // Filter
            ->when($request->status, function ($q) use ($request) {
                return $q->where('transaction_status', $request->status);
            })
            ->when($request->transaction_number, function ($q) use ($request) {
                return $q->where('transaction_number', 'like', '%'.$request->transaction_number.'%');
            })
            ->when($request->dates, function ($q) use ($request) {
                $dates = json_decode($request->dates);
                return $q->whereBetween('transaction_date', [$dates->start, $dates->end]);
            })

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

        $transaction_details = TransactionDetail::whereTransactionId($transaction_id)->with("transactions")->get();

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
                $transaction_detail_user->ticket_user_type = $detail->transactions->transaction_type;

                // ADD DENORMALIZE DATA
                $transaction_detail_user->ticket_id = $detail->ticket_id;
                $transaction_detail_user->ticket_name = $detail->ticket_name;
                $transaction_detail_user->class_id = $detail->class_id;
                $transaction_detail_user->class_name = $detail->class_name;
                $transaction_detail_user->event_id = $detail->event_id;
                $transaction_detail_user->event_name = $detail->event_name;
                $transaction_detail_user->user_name = $detail->user_name;

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
        $validation = Validator::make($request->all(), [
            'event_id'      => 'required|integer|exists:events,id',
            'user_id'       => 'required|integer|exists:users,id',
            'ticket_type'   => 'required|string|in:regular,community',
            'total_price'   => 'required|numeric|min:0',
            'discount'      => 'required|numeric|min:0',
            'subtotal_price'=> 'required|numeric|min:0',
            'unique_code'   => 'required|integer|min:100|max:999',
            'total_qty'     => 'required|integer|min:0',
            'details' => 'array|min:1',
            'details.*.type'         => 'required|string|in:bundle,piece',
            'details.*.qty'          => 'required|integer|min:1',
            'details.*.price'        => 'required|numeric|min:0',
            'details.*.subtotal_price' => 'required|numeric|min:0',
        ]);

        if ($validation->fails()) {
            return $this->validationErrorResponse($validation->errors()->first(), $validation->errors());
        }

        DB::beginTransaction();

        try {
            $user = User::find($request->user_id);
            // Payment Limit Date + 15Menit
            $payment_limt_date = now()->addMinutes(15);

            // GET DATA FOR DENORMALIZE
            $event = Event::where('id', $request->event_id)->first();

            $transaction_api_controller = new \App\Http\Controllers\API\TransactionController();
            $transaction = Transaction::create([
                'event_id'      => $request->event_id,
                'user_id'       => $request->user_id,
                'user_name'     => $user->name,
                'transaction_date' => now(),
                'transaction_number' => $transaction_api_controller->generateInvoiceNumberTransactions(),
                'total_price'   => $request->total_price,
                'discount_price'      => $request->discount,
                'subtotal_price'=> $request->subtotal_price,
                'unique_code_price'   => $request->unique_code,
                'transaction_status' => 'unpaid',
                'transaction_type' => $request->ticket_type,
                'payment_limit_date' => $payment_limt_date,
                'is_from_panel' => 1,
                'event_name' => $event->name,
            ]);

            $data_details_bundle = collect($request->details)->where("type","bundle");
            // Get The ids
            if ($data_details_bundle->count() > 0) {
                $ticket_bundle_ids = $data_details_bundle->pluck('id')->toArray();
                $ticket_bundles = TicketBundle::whereIn('id', $ticket_bundle_ids)->get();
            }

            $transaction_details = [];
            foreach ($request->details as $detail) {
                // Bundle
                if ($detail['type'] == 'bundle') {
                    $ticket_bundle_id = $detail['id'];

                    $ticket_bundle = $ticket_bundles->where('id', $ticket_bundle_id)->first();

                    if (!$ticket_bundle) {
                        DB::rollBack();
                        return $this->errorResponse('Ticket bundle not found');
                    }

                    $qty = $detail['qty'];
                    foreach ($detail['tickets'] as $piece) {
                        $transaction_detail = TransactionDetail::create([
                            'transaction_id' => $transaction->id,
                            'ticket_bundle_id' => $ticket_bundle_id,
                            'ticket_id' => $piece['id'],
                            'ticket_bundle_name' => $ticket_bundle->name,
                            'ticket_name' => $piece['ticket_name'],
                            'qty'            => $qty,
                            'price'          => $detail['price'],
                            'subtotal_price' => $detail['subtotal_price'],
                            'user_id' => $request->user_id,
                            'user_code' => $user->user_code,
                            'user_name' => $user->name,
                            'event_name' => $event->name,
                            'event_id' => $event->id,
                            'class_id' => $piece['class_id'],
                            'class_name' => $piece['class_name'],
                        ]);

                        array_push($transaction_details, $transaction_detail);
                    }
                } else {
                    // Piece
                    $transaction_detail = TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'ticket_bundle_id' => null,
                        'ticket_id'      => $detail['id'],
                        'ticket_bundle_name' => null,
                        'ticket_name'    => $detail['ticket_name'],
                        'user_name'      => $user->name,
                        'user_id'        => $request->user_id,
                        'user_code'      => $user->user_code,
                        'qty'            => $detail['qty'],
                        'price'          => $detail['price'],
                        'subtotal_price' => $detail['subtotal_price'],
                        'event_name' => $event->name,
                        'event_id' => $event->id,
                        'class_id' => $detail['class_id'],
                        'class_name' => $detail['class_name'],
                    ]);

                    array_push($transaction_details, $transaction_detail);
                }
            }

            // Handle QTY Stock Tickets
            $this->stock_errors = [];
            $this->handleStockTickets($transaction->id, $request->details);
            if (count($this->stock_errors) > 0) {
                DB::rollBack();
                return $this->errorResponse('error_stocks', $this->stock_errors);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }

        $response = [
            'transaction' => $transaction,
            'transaction_details' => $transaction_details
        ];
        return $this->createSuccessResponse('Transaction success', $response);
    }

    private function handleStockTickets($transaction_id, $details)
    {
        try {
            // Pluck Ticket Id on Detail Bundle And Pice
            $ticket_ids = [];
            foreach ($details as $detail) {
                if ($detail['type'] == 'bundle') {
                    foreach ($detail['tickets'] as $bundle) {
                        $ticket_ids[] = $bundle['id'];
                    }
                } else {
                    $ticket_ids[] = $detail['id'];
                }
            }

            // Sort ID Array ASC
            sort($ticket_ids);

            // Lock Ticket by Ids Sorten By ASC
            $tickets = Ticket::whereIn('id', $ticket_ids)->lockForUpdate()->get();

            foreach ($details as $detail) {
                if ($detail['type'] == 'bundle') {
                    foreach ($detail['tickets'] as $bundle) {
                        $ticket = $tickets->where('id', $bundle['id'])->first();
                        $ticket->quota_left = $ticket->quota_left - $detail['qty'];

                        // If Quota Left < 0
                        if ($ticket->quota_left < 0) {
                            $this->stock_errors[] = [
                                'message' => 'Out of stock',
                                'id_ticket' => $detail['id'],
                                'type' => 'bundle'
                            ];
                            continue;
                        }

                        $ticket->save();
                    }
                } else {
                    $ticket = $tickets->where('id', $detail['id'])->first();
                    $ticket->quota_left = $ticket->quota_left - $detail['qty'];

                    // If Quota Left < 0
                    if ($ticket->quota_left < 0) {
                        $this->stock_errors[] = [
                            'message' => 'Out of stock',
                            'id_ticket' => $ticket['id'],
                            'type' => 'piece'
                        ];
                        continue;
                    }

                    $ticket->save();
                }
            }
        } catch (\Exception $e) {
           throw new Exception($e->getMessage());
        }
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
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return $this->errorResponse('Transaction not found');
        }

        DB::beginTransaction();
        try {
            // Rollback Stock
            $this->rollbackStock($id);

            // Delete Transaction
            $transaction->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }
}
