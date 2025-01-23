<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\GeneralLedger;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends ApiController
{
    public function index(Request $request)
    {
        $data = Transaction::paginate($request->per_page ?? 10);
        return $this->successResponse("Success", $data);
    }

    public function store(Request $request)
    {
        $auth_user = $request->auth_user;
        $data = $request->all();

        $trx_payload = [
            'type' => $data['type'],
            'date' => $data['date'],
            'name' => $data['name'],
            'description' => $data['description'],
            'user_id' => $auth_user['id'],
        ];

        $trx_detail_payload = [];

        foreach ($data['details'] as $item) {
            $trx_detail_payload[] = [
                'description' => $item['description'] ?? "",
                'account' => $item['account'],
                'subaccount' => $item['subaccount'],
                'order' => $item['order'],
                'debit' => $item['debit'],
                'credit' => $item['credit'],
            ];
        }

        // Validasi balance debit credit
        $items = collect($trx_detail_payload);
        $sum = $items->sum('debit') - $items->sum('credit');

        if ($sum != 0) {
            return $this->errorResponse("Debit dan Credit tidak balance");
        }

        $trx_payload['total_debit'] = $items->sum('debit');
        $trx_payload['total_credit'] = $items->sum('credit');

        DB::transaction(function () use ($trx_payload, $trx_detail_payload, $data) {
            $trx = Transaction::create($trx_payload);

            foreach ($trx_detail_payload as $item) {
                $item['transaction_id'] = $trx->id;
                $item['transaction_type'] = $data['type'];
                $item['date'] = $data['date'];

                GeneralLedger::create($item);
            }
        });

        return $this->successResponse("Success");
    }

    public function update(Request $request, $id)
    {
        $auth_user = $request->auth_user;
        $data = $request->all();

        $trx_payload = [
            'type' => $data['type'],
            'date' => $data['date'],
            'name' => $data['name'],
            'description' => $data['description'],
            'user_id' => $auth_user['id'],
        ];

        $trx_detail_payload = [];

        foreach ($data['details'] as $item) {
            $trx_detail_payload[] = [
                'description' => $item['description'] ?? "",
                'account' => $item['account'],
                'subaccount' => $item['subaccount'],
                'order' => $item['order'],
                'debit' => $item['debit'],
                'credit' => $item['credit'],
            ];
        }

        // Validasi balance debit credit
        $items = collect($trx_detail_payload);
        $sum = $items->sum('debit') - $items->sum('credit');

        if ($sum != 0) {
            return $this->errorResponse("Debit dan Credit tidak balance");
        }

        $trx_payload['total_debit'] = $items->sum('debit');
        $trx_payload['total_credit'] = $items->sum('credit');

        DB::transaction(function () use ($trx_payload, $trx_detail_payload, $data, $id) {
            Transaction::find($id)->update($trx_payload);

            foreach ($trx_detail_payload as $item) {
                $gl = GeneralLedger::where('transaction_id', $id)->where('order', $item['order'])->first();

                if ($gl) {
                    $gl->update($item);
                }
            }
        });

        return $this->successResponse("Success");
    }

    public function destroy(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            $general_ledger = GeneralLedger::where('transaction_id', $id)->delete();
            $data = Transaction::find($id)->delete();
        });

        return $this->successResponse("Success");
    }
}
