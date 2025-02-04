<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Cart;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends ApiController
{

    // ADD TO CART
    private $user;
    private $response_ticket;
    private $ticket_stock_check;
    private $event_id;
    // END ADD TO CART

    public function getCarts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'event_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Errors", $validator->errors());
        }

        try {
            $this->user = $request->auth_user;
            $event_id = $request->input('event_id');
            $carts = Cart::leftJoin("tickets", "carts.ticket_id", "=", "tickets.id")
                ->select("carts.*", "tickets.quota_left")->whereUserId($this->user->id)->where("carts.event_id",$event_id)->get();


            $carts = $carts->map(function ($cart) {
                //CHECK OUT OF STOCK
                if ($cart->qty > $cart->quota_left) {
                    $cart->out_of_stock = true;
                } else {
                    $cart->out_of_stock = false;
                }

                return $cart;
            });

            $total_cart_price = $carts->sum('total_price');
            $total_cart_qty = $carts->sum('qty');

            $cart_ticket_bundle_id = $carts->pluck('ticket_bundle_id')->toArray();

            $ticket_bundles = TicketBundle::select("id as ticket_bundle_id","name","price")->whereIn('id', $cart_ticket_bundle_id)->get();
            if ($ticket_bundles->count() > 0) {
                $ticket_bundles = $ticket_bundles->map(function ($ticket_bundle) use ($carts) {
                    $ticket_bundle->bundle_qty = $carts->where('ticket_bundle_id', $ticket_bundle->ticket_bundle_id)->first()->qty;
                    $ticket_bundle->tickets = $carts->where('ticket_bundle_id', $ticket_bundle->ticket_bundle_id)->flatten();

                    return $ticket_bundle;
                });
            }

            $carts = $carts->filter(function ($cart_item) {
                return $cart_item->ticket_bundle_id == null;
            })->flatten();

            $response = [
                "events" => Event::select("id as event_id","name as event_name")->find($event_id),
                "ticket_bundles" => $ticket_bundles,
                "ticket_pieces" => $carts,
                "total_cart_price" => $total_cart_price,
                "total_cart_qty" => $total_cart_qty,
            ];

            return $this->successResponse("Success", $response);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }

    public function addToCarts(Request $request)
    {
        $this->user = $request->auth_user;

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:bundle,piece',
            'ticket_id' => 'required',
            'event_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Errors", $validator->errors());
        }

        $type = $request->input('type');
        $ticket_id = $request->input('ticket_id');
        $this->event_id = $request->input('event_id');

        DB::beginTransaction();
        try {
            $this->response_ticket = [];
            $this->ticket_stock_check = [];

            if ($type == 'bundle') {
                // Validation if ticket id is not found
                $tickets = Ticket::whereTicketBundleId($ticket_id)->get();

                if ($tickets->count() == 0) {
                    DB::rollBack();
                    return $this->errorResponse("Ticket not found");
                }

                $this->bundleToCart($tickets);
            } else {
                // Validation if ticket id is bundle but wrong type
                $ticket = Ticket::find($ticket_id);
                if ($ticket->ticket_bundle_id) {
                    DB::rollBack();
                    return $this->errorResponse("Ticket is bundle type");
                }
                $this->pieceToCart($ticket);
            }

            if (count($this->ticket_stock_check) > 0) {
                DB::rollBack();
                return $this->errorResponse("Errors", $this->ticket_stock_check);
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->errorResponse($ex->getMessage());
        }

        return $this->createSuccessResponse("Success", $this->response_ticket);
    }

    private function bundleToCart($tickets)
    {
        foreach ($tickets as $ticket) {
            $this->pieceToCart($ticket);
        }
    }

    private function pieceToCart($ticket)
    {

        // IF TICKET EXIST ON CART
        $cart = Cart::whereUserId($this->user->id)->whereTicketId($ticket->id)->first();

        // CHECK STOCK
        $qty_cart = $cart ? $cart->qty : 0;
        if (($qty_cart + 1) > $ticket->quota_left) {
            $this->ticket_stock_check[] = [
                'ticket_id' => $ticket->id,
                'message' => 'Ticket out of stock',
            ];
            return;
        }

        if ($cart) {
            $cart->qty += 1;
            $cart->total_price = $cart->price * $cart->qty;
            $cart->save();
        } else {
            $cart = new Cart();
            $cart->user_id = $this->user->id;
            $cart->ticket_id = $ticket->id;
            $cart->ticket_bundle_id = $ticket->ticket_bundle_id;
            $cart->price = $ticket->price;
            $cart->qty = 1;
            $cart->total_price = $ticket->price;
            $cart->event_id = $this->event_id;
            $cart->save();
        }

        $this->response_ticket[] = $cart;
    }

    public function deleteBundleTicket($id, Request $request)
    {
        $user = $request->auth_user;

        $carts = Cart::whereUserId($user->id)->whereTicketBundleId($id)->get();

        // CHECK IF CART NOT FOUND
        if ($carts->count() == 0) {
            return $this->errorResponse("Cart not found");
        }

        // DELETE CART
        $carts->each(function ($cart) {
            $cart->delete();
        });

        return $this->successResponse("Success");
    }

    public function deletePieceTicket($id, Request $request)
    {
        $user = $request->auth_user;

        $cart = Cart::whereUserId($user->id)->whereTicketId($id)->whereNull("ticket_bundle_id")->first();

        // CHECK IF CART NOT FOUND
        if (!$cart) {
            return $this->errorResponse("Cart not found");
        }

        // DELETE CART
        $cart->delete();

        return $this->successResponse("Success");
    }

    public function handleCalculationQtyCart(Request $request)
    {

        /*
         * type (change : directly update the cart qty, add, substract)
         * ticket bundle id (optional)
         * ticket id (optional)
         * must fill ticket bundle id or ticket id
         * */

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:add,substract,change',
            'ticket_bundle_id' => 'required_without:ticket_id',
            'ticket_id' => 'required_without:ticket_bundle_id',
            'qty' => 'required_if:type,change',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Errors", $validator->errors());
        }

        $type = $request->input('type');
        $ticket_type = $request->input('ticket_bundle_id') ? 'bundle' : 'piece';

        $this->user = $request->auth_user;

        DB::beginTransaction();

        try {
            $this->ticket_stock_check = [];
            $this->response_ticket = [];
            if ($ticket_type == 'bundle') {
                $this->calculateBundleCartTicket($request->input('ticket_bundle_id'), $type, $request);
            } else {
                $this->calculatePieceCartTicket($request->input('ticket_id'), $type, $request);
            }

            if (count($this->ticket_stock_check) > 0) {
                DB::rollBack();
                return $this->errorResponse("Errors", $this->ticket_stock_check);
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->errorResponse($ex->getMessage());
        }

        return $this->successResponse("Success", $this->response_ticket);
    }

    private function calculateBundleCartTicket($ticket_bundle_id, $type, Request $request)
    {
        $carts = Cart::whereUserId($this->user->id)
            ->select("carts.*","tickets.quota_left")
            ->leftJoin('tickets', 'carts.ticket_id', '=', 'tickets.id')
            ->where("carts.ticket_bundle_id",$ticket_bundle_id)->get();

        if ($carts->count() == 0) {
            $this->ticket_stock_check[] = [
                'ticket_bundle_id' => $ticket_bundle_id,
                'message' => 'Ticket not found in cart',
            ];
            return;
        }

        /*Check Ticket Qty*/
        foreach ($carts as $cart) {

            if ($type == 'change') {
                $cart->qty = $request->input('qty');
            } else {
                $cart->qty = $type == 'add' ? $cart->qty + 1 : $cart->qty - 1;
            }

            if ($cart->qty > $cart->quota_left) {
                $this->ticket_stock_check[] = [
                    'ticket_id' => $cart->ticket_id,
                    'message' => 'Ticket out of stock',
                ];
            } else if ($cart->qty == 0) {
                $this->response_ticket[] = $cart;
                // DELETE CART
                $cart->delete();
            } else {
                $cart->total_price = $cart->price * $cart->qty;
                $cart->save();

                $this->response_ticket[] = $cart;
            }
        }
    }

    private function calculatePieceCartTicket($ticket_id, $type, Request $request)
    {
        $cart = Cart::whereUserId($this->user->id)->whereTicketId($ticket_id)->first();
        $ticket = Ticket::find($ticket_id);

        if (!$cart) {
            $this->ticket_stock_check[] = [
                'ticket_id' => $ticket_id,
                'message' => 'Ticket not found in cart',
            ];
            return;
        }

        if ($type == 'change') {
            $cart->qty = $request->input('qty');
        } else {
            $cart->qty = $type == 'add' ? $cart->qty + 1 : $cart->qty - 1;
        }
        if ($cart->qty > $ticket->quota_left) {
            $this->ticket_stock_check[] = [
                'ticket_id' => $ticket_id,
                'message' => 'Ticket out of stock',
            ];
        } else if ($cart->qty == 0) {
                $this->response_ticket[] = $cart;
                // DELETE CART
                $cart->delete();
        } else {
            $cart->total_price = $cart->price * $cart->qty;
            $cart->save();

            $this->response_ticket[] = $cart;
        }
    }
}
