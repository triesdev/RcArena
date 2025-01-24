<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Cart;
use App\Models\EventClass;
use App\Models\GeneralLedger;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends ApiController
{
    public function index(Request $request)
    {
        $data = Transaction::paginate($request->per_page ?? 10);
        return $this->successResponse("Success", $data);
    }

    public function show($id)
    {
        $data = Transaction::with(['transaction_details' => function ($q) {
                return;
            },'event_classes'])
            ->find($id);

        return $data;

        if (!$data) {
            return $this->errorResponse("Data not found");
        }
        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $auth_user = $request->auth_user;

        $validator = Validator::make($request->all(), [
            'event_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Errors", $validator->errors());
        }

        DB::beginTransaction();
        try {

            // GET CARTS USER
            $carts = Cart::where('user_id', $auth_user->id)
                ->leftJoin('ticket_bundles', 'carts.ticket_bundle_id', '=', 'ticket_bundles.id')
                ->leftJoin('tickets', 'carts.ticket_id', '=', 'tickets.id')
                ->select("carts.*","ticket_bundles.name as ticket_bundle_name","tickets.quota_left")
                ->where('carts.event_id', $request->event_id)
                ->get();

            if ($carts->count() == 0) {
                DB::rollBack();
                return $this->errorResponse("Cart is empty");
            }

            // CHECK CARTS STOCK
            foreach ($carts as $cart) {
                if ($cart->qty > $cart->quota_left) {
                    DB::rollBack();
                    return $this->errorResponse("Error",[
                        'message' => 'Out of stock',
                        'id_ticket' => $cart->ticket_id
                    ]);
                }
            }

            // Transaction DateTime Now
            $transaction_date = date('Y-m-d H:i:s');

            // Unique Code Price 100 - 500
            $unique_code_price = rand(100, 500);
            $total_cart_prices = $carts->sum('total_price');

            $transaction = new Transaction();
            $transaction->user_id = $auth_user->id;
            $transaction->event_id = $request->event_id;
            $transaction->user_name = $auth_user->name;
            $transaction->transaction_date = $transaction_date;
            $transaction->transaction_number = $this->generateInvoiceNumberTransactions();
            $transaction->unique_code_price = $unique_code_price;
            $transaction->total_price = $total_cart_prices + $unique_code_price;
            $transaction->save();

            // LOCK FOR UPDATE TICKET QUOTA LEFT

            $ticket_ids_ordered_asc = $carts->pluck('ticket_id')->toArray();
            sort($ticket_ids_ordered_asc);

            $tickets = Ticket::whereIn('id', $ticket_ids_ordered_asc)->sharedLock()->get();

            // INSERT Transaction Details
            $transaction_detail_response = [];
            foreach ($carts as $cart) {
                $transaction_detail = new TransactionDetail();
                $transaction_detail->transaction_id = $transaction->id;
                $transaction_detail->user_id = $auth_user->id;
                $transaction_detail->ticket_id = $cart->ticket_id;
                $transaction_detail->ticket_bundle_id = $cart->ticket_bundle_id;
                $transaction_detail->ticket_bundle_name = $cart->ticket_bundle_name ?? null;
                $transaction_detail->user_name = $auth_user->name;
                $transaction_detail->user_code = $auth_user->user_code;
                $transaction_detail->qty = $cart->qty;
                $transaction_detail->price = $cart->price;
                $transaction_detail->subtotal_price = $cart->qty * $cart->price;
                $transaction_detail->save();

                // Update Quota Left
                $ticket = $tickets->where('id', $cart->ticket_id)->first();
                $ticket->quota_left = $ticket->quota_left - $cart->qty;
                if ($ticket->quota_left < 0) {
                    DB::rollBack();
                    return $this->errorResponse("Error",[
                        'message' => 'Out of stock',
                        'id_ticket' => $ticket->id
                    ]);
                }
                $ticket->save();

                $transaction_detail_response[] = $transaction_detail;
            }

            $transaction->transaction_detail = $transaction_detail_response;

            // DELETE CARTS
            Cart::where('user_id', $auth_user->id)->delete();

            DB::commit();
            return $this->successResponse("Success", $transaction);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->errorResponse($ex->getMessage());
        }
    }

    private function generateInvoiceNumberTransactions(){
        // Generate Invoice Number LIKE (INV/2024/V/00001)
        $year = date('Y');
        $month = date('m');
        $roman_month = $this->convertToRoman($month);
        $last_invoice = Transaction::where('transaction_number', 'like', 'INV/'.$year.'/'.$roman_month.'%')->orderBy('id', 'desc')->first();

        if($last_invoice){
            $last_invoice_number = explode('/', $last_invoice->transaction_number);
            $last_invoice_number = $last_invoice_number[3];
            $new_invoice_number = $last_invoice_number + 1;
            $new_invoice_number = str_pad($new_invoice_number, 5, '0', STR_PAD_LEFT);
            $new_invoice_number = 'INV/'.$year.'/'.$roman_month.'/'.$new_invoice_number;
        }else{
            $new_invoice_number = 'INV/'.$year.'/'.$roman_month.'/00001';
        }

        return $new_invoice_number;
    }

    private function convertToRoman($month){
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[(int)$month];
    }
}
