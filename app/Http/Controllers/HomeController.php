<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Budget;
use App\Models\FinancialYear;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    function index()
    {

        $bankCount = Bank::count();

        $accounts = Account::whereNull('is_cash_account')
            ->get();

        //get active financial Year

        $setting = Setting::first();

        $financialYear = FinancialYear::where('id', $setting->active_financial_year_id)
            ->first();

        $fdrAccountCount = $accounts->where('type', 'FDR')->count();
        $stdAccountCount = $accounts->where('type', 'STD')->count();

        $fdrAmount = Transaction::select(DB::raw("SUM(IF(account_type='credit', amount, -amount)) as balance"))
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('accounts.type', 'FDR')
            ->where('status', 'final')
            ->whereBetween('date', [$financialYear->start_date, $financialYear->end_date])
            ->get()
            ->first()->balance;

        $stdAmount = Transaction::select(DB::raw("SUM(IF(account_type='credit', amount, -amount)) as balance"))
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('accounts.type', 'STD')
            ->where('status', 'final')
            ->whereBetween('date', [$financialYear->start_date, $financialYear->end_date])
            ->get()
            ->first()->balance;

        $allBudgets = Budget::where('date', [$financialYear->start_date, $financialYear->end_date])
            ->get();

        $totalExpenseBudget = $allBudgets->where('type', 'expense')->sum('amount');

        $totalIncomeBudget = $allBudgets->where('type', 'income')->sum('amount');

        return view('home.dashboard', compact('bankCount', 'fdrAccountCount', 'stdAccountCount', 'stdAmount', 'fdrAmount', 'totalExpenseBudget', 'totalIncomeBudget', 'financialYear'));
    }
}
