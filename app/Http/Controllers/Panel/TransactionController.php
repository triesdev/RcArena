<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Repository\TransactionRepository;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->paginate(10);

        return $this->successResponse('Transaction list', $transactions);
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
