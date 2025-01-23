<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Account;
use App\Models\GeneralLedger;
use Illuminate\Http\Request;

class ReportController extends ApiController
{
    public function balanceSheet(Request $request)
    {
        $ledgers = GeneralLedger::groupBy('subaccount')
            ->selectRaw('*, sum(debit) as sum_debit, sum(credit) as sum_credit')
            ->get();

        $data = Account::select(
            "category",
            "type",
            "account",
            "subaccount",
            "post_saldo",
            "name",
        )->get();

        for ($i = 0; $i < count($data); $i++) {
            $sub_debit = $ledgers->where('subaccount', $data[$i]->subaccount)->sum('sum_debit');
            $sub_credit = $ledgers->where('subaccount', $data[$i]->subaccount)->sum('sum_credit');

            if ($data[$i]->post_saldo == 'debit') {
                $data[$i]['balance'] = $sub_debit - $sub_credit;
            } else {
                $data[$i]['balance'] = $sub_credit - $sub_debit;
            }
        }

        $accounts = $data->where('type', 'account');
        for ($i = 0; $i < count($accounts); $i++) {
            $children = [
                [
                    ...$accounts[$i]->toArray(),
                    "name" => "Uncategorized " . $accounts[$i]->name,
                ]
            ];

            $subaccounts = $data->where('account', $accounts[$i]->account)->where('type', 'subaccount');
            if (count($subaccounts) > 0) {
                foreach ($subaccounts as $j => $subaccount) {
                    $children[] = $subaccount;
                }
            }

            $accounts[$i]['children'] = $children;

            $accounts[$i]['balance'] = collect($children)->sum('balance');
        }

        $collect_account = collect($accounts);
        $result = [];

        $asset = $collect_account->where('category', 'asset')->sum('balance');
        if ($request->asset) {
            $result['asset'] = [
                "name" => "Asset",
                "balance" => $collect_account->where('category', 'asset')->sum('balance'),
                "item" => $collect_account->where('category', 'asset')->flatten(),
            ];
        }

        $liability = $collect_account->where('category', 'liability')->sum('balance');
        if ($request->liability) {
            $result['liability'] = [
                "name" => "Liability",
                "balance" => $collect_account->where('category', 'liability')->sum('balance'),
                "item" => $collect_account->where('category', 'liability')->flatten(),
            ];
        }

        $revenue = $collect_account->where('category', 'revenue')->sum('balance');
        if ($request->revenue) {
            $result['revenue'] = [
                "name" => "Revenue",
                "balance" => $revenue,
                "item" => $collect_account->where('category', 'revenue')->flatten(),
            ];
        }

        $expense = $collect_account->where('category', 'expense')->sum('balance');
        if ($request->expense) {
            $result['expense'] = [
                "name" => "Expense",
                "balance" => $collect_account->where('category', 'expense')->sum('balance'),
                "item" => $collect_account->where('category', 'expense')->flatten(),
            ];
        }

        $result['profit_loss'] = $revenue - $expense;

        $equity = $collect_account->where('category', 'equity')->sum('balance');
        if ($request->equity) {
            $item = $collect_account->where('category', 'equity')->flatten();

            for ($i = 0; $i < count($item); $i++) {
                if ($item[$i]['310400']) {
                    $item[$i]['balance'] += $result['profit_loss'];
                }
            }

            $result['equity'] = [
                "name" => "Equity",
                "balance" => $collect_account->where('category', 'equity')->sum('balance'),
                "item" => $item,
            ];
        }

        $result['balance'] = $asset - $liability - $equity + $result['profit_loss'];

        return $this->successResponse("Successs", $result);
    }
}
