<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Cart;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketBundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                $this->bundleToCart($ticket_id);
            } else {
                $this->pieceToCart($ticket_id);
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

    private function bundleToCart($bundle_id)
    {
        // LOCK FOR UPDATE TICKET BY TICKET BUNDLE ID
        $tickets = Ticket::whereTicketBundleId($bundle_id)->get();

        foreach ($tickets as $ticket) {
            $this->pieceToCart($ticket->id);
        }
    }

    private function pieceToCart($ticket_id)
    {
        $ticket = Ticket::find($ticket_id);

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
}
