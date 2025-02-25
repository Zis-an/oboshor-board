<?php

namespace App\Http\Controllers;

use App\Exports\CashbookReportExport;
use App\Exports\ReportExport;
use App\Models\Account;
use App\Models\FinancialYear;
use App\Models\Lot;
use App\Models\LotItem;
use App\Models\Setting;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ReportController extends Controller {

    private $financialYear;
    private $financialYearRange;

    function __construct() {
        ini_set('memory_limit', '2048M');
        $setting = Setting::first();
        $fy = FinancialYear::find($setting->active_financial_year_id);
        if (!empty($fy)) {
            $this->financialYearRange = implode('~', [$fy->start_date, $fy->end_date]);
            $this->financialYear = $fy;
        }
    }

    function bankReport() {

        $accounts = Account::getAccounts();

        if (\request()->ajax()) {
            $date = \request()->input('date');

            $account_id = \request()->input('account_id');

            $query = Transaction::with('account')->join('lot_items', 'lot_items.id', '=', 'transactions.lot_item_id')
                    ->join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->whereNotNull('lot_item_id')
                    ->select('transactions.id', 'transactions.date', 'transactions.amount', 'lots.lot_number', 'lot_items.index');

            if (!empty($account_id)) {
                $query->where('transactions.account_id', $account_id);
            }

            $dateRange = explode('~', $date);
            $query->whereBetween('transactions.date', [$dateRange[0], $dateRange[1]]);

            $lotTableData = $query->get();

            //before balance

            $balanceUntilDate = Transaction::
                            select([
                                DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                            ->whereDate('date', '<', $dateRange[0])
                            ->where('status', 'final')
                            ->orderBy('date')
                            ->first()->balance;

            $bankCharge = Transaction::
                            //where('account_id', $accountId)
                            select([
                                DB::raw('SUM(amount) as balance')])
                            ->whereBetween('date', $dateRange)
                            ->where('status', 'final')
                            ->where('type', 'service-charge')
                            ->where('account_type', 'debit')
                            ->orderBy('date')
                            ->first()->balance;

            //all debit except service charge and lot items
            $totalExpense = Transaction::
                            //where('account_id', $accountId)
                            select([
                                DB::raw('SUM(amount) as balance')])
                            ->whereBetween('date', $dateRange)
                            ->where('status', 'final')
                            ->where('type', '!', 'service-charge')
                            ->whereNull('lot_item_id')
                            ->where('account_type', 'debit')
                            ->orderBy('date')
                            ->first()->balance;

            $totalIncome = Transaction::
                            //where('account_id', $accountId)
                            select([
                                DB::raw('SUM(amount) as balance')])
                            ->whereBetween('date', $dateRange)
                            ->where('status', 'final')
                            ->where('type', 'income')
                            ->orderBy('date')
                            ->first()->balance;

            //Lot return

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'returned')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('transactions.account_id', $account_id);
            }

            $transactionQuery = Transaction::orderBy('date')
                    ->whereBetween('date', $dateRange)
                    ->where('status', 'final');

            if (!empty($account_id)) {
                $transactionQuery->where('transactions.account_id', $account_id);
            }

            $lotReturns = $lotReturnsQuery->get();

            $transactions = $transactionQuery->get();

            $accountBook = $lotTableData;

            $lotTable = view('report.bank-report-table', compact('bankCharge', 'totalExpense', 'totalIncome', 'transactions', 'lotReturns'))
                    ->with(['data' => $lotTableData, 'accountBook' => $accountBook])
                    ->render();

            return response()->json(['lotTable' => $lotTable]);
        }

        return view('report.bank-report', compact('accounts'));
    }

    function incomeReport() {

        if (\request()->ajax()) {

            $date = \request()->input('date');

            $dateRange = explode('~', $date);

            $incomes = Transaction::where('type', 'income')
                    ->whereBetween('date', $dateRange)
                    ->orderBy('date', 'desc');

            return \Yajra\DataTables\Facades\DataTables::of($incomes)
                            ->make(true);
        }

        return view('report.income-report');
    }

    function employeePaymentReport(Request $request) {

        if (\request()->ajax()) {

            $date = $request->input('date');

            $date = $request->input('account_id');

            $query = Transaction::with('account')->join('lot_items', 'lot_items.id', '=', 'transactions.lot_item_id')
                    ->join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->whereNotNull('lot_item_id')
                    ->select('transactions.id', 'transactions.date', 'transactions.amount', 'lots.lot_number', 'lot_items.index');

            if (!empty($account_id)) {
                $query->where('transactions.account_id', $account_id);
            }

            $dateRange = explode('~', $date);
            $query->whereBetween('transactions.date', [$dateRange[0], $dateRange[1]]);

            DataTables::of($query)
                    ->make(true);
        }

        return view('report.employee-payment-report');
    }

    function expenseReport() {
        if (\request()->ajax()) {

            $date = \request()->input('date');

            $dateRange = explode('~', $date);

            $incomes = Transaction::where('type', 'expense')
                    ->whereBetween('date', $dateRange)
                    ->orderBy('date', 'desc');

            return DataTables::of($incomes)
                            ->make(true);
        }

        return view('report.expense-report');
    }

    function fdrReport() {


        if (\request()->ajax()) {

            $date = \request()->input('date');

            $dateRange = explode('~', $date);

            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $accountTransactions = Account::with(['bank', 'branch'])->select('accounts.*',
                            DB::raw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id) as balance"),
                            DB::raw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type = 'credit' AND transactions.date BETWEEN ? AND ?) as income",),
                            DB::raw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type = 'debit' AND transactions.date BETWEEN ? AND ?) as expense"
                            ))
                    ->setBindings([$startDate, $endDate, $startDate, $endDate]);

            return \Yajra\DataTables\Facades\DataTables::of($accountTransactions)
                            ->editColumn('balance', function ($row) {
                                return number_format($row->balance, 2);
                            })
                            ->editColumn('status', function ($row) {

                                if ($row->closed_at) {
                                    return "<span class='badge badge-warning'>Closed</span>";
                                } else {
                                    return "<span class='badge badge-success'>Active</span>";
                                }
                            })
                            ->rawColumns(['actions', 'status'])
                            ->make(true);
        }

        return view("report.fdr-report");
    }

    function bankCashbookReport() {

        if (\request()->ajax()) {

            $date = \request()->input('date');

            $dateRange = explode('~', $date);
            $start = $dateRange[0];

            $end = $dateRange[1];

            $account_id = \request()->input('account_id');

            $openingBalance = Transaction::
                            where('account_id', $account_id)
                            ->select([
                                DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                            ->whereDate('date', '<', $dateRange[0])
                            ->where('status', 'final')
                            ->orderBy('date')
                            ->first()->balance;

            $transactionQuery = Transaction::orderBy('date')
                    ->leftJoin('lot_items', 'lot_items.id', '=', 'transactions.lot_item_id')
                    ->leftJoin('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->whereBetween('transactions.date', $dateRange)
                    ->where('transactions.status', 'final')
                    ->select('transactions.id', 'transactions.date',
                    'transactions.amount', 'transactions.account_type',
                    'transactions.lot_item_id',
                    'lot_items.index', 'lots.name as lot_name', 'transactions.description',
            );

            if (!empty($account_id)) {
                $transactionQuery->where('transactions.account_id', $account_id);
            }

            $transactions = $transactionQuery->get();

            return view("report.bank.cashbook.table", compact('transactions', 'openingBalance', 'start', 'end'));
        }

        $accounts = Account::getAccounts();

        return view('report.bank.cashbook.report', compact('accounts')
                )->with(['financialYearRange' => $this->financialYearRange]);
    }

    function bankCashBookReportExportPDF() {
        $date = \request()->input('date');

        $dateRange = explode('~', $date);

        $start = $dateRange[0];

        $end = $dateRange[1];

        $account_id = \request()->input('account');

        $openingBalance = Transaction::
                        where('account_id', $account_id)
                        ->select([
                            DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                        ->whereDate('date', '<', $dateRange[0])
                        ->where('status', 'final')
                        ->orderBy('date')
                        ->first()->balance;

        $transactionQuery = Transaction::orderBy('date')
                ->leftJoin('lot_items', 'lot_items.id', '=', 'transactions.lot_item_id')
                ->leftJoin('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->whereBetween('transactions.date', $dateRange)
                ->where('transactions.status', 'final')
                ->select('transactions.id', 'transactions.date',
                'transactions.amount', 'transactions.account_type',
                'transactions.lot_item_id',
                'lot_items.index', 'lots.name as lot_name', 'transactions.description',
        );

        if (!empty($account_id)) {
            $transactionQuery->where('transactions.account_id', $account_id);
        }

        $transactions = $transactionQuery->get();

        $setting = Setting::first();
        $financialYear = FinancialYear::where('id', $setting->active_financial_year_id)
                ->first();

        $totalDebit = $transactions->where('account_type', 'debit')->sum('amount');
        $totalCredit = $transactions->where('account_type', 'credit')->sum('amount');

        $type = \request()->input('type');
        $data['totalDebit'] = $totalDebit;
        $data['totalCredit'] = $totalCredit;
        $data['closingBalance'] = $openingBalance + ($totalCredit - $totalDebit);
        $data['transactions'] = $transactions;
        $data['openingBalance'] = $openingBalance;
        $data['start'] = $start;
        $data['end'] = $end;
        $data['financialYear'] = $financialYear->name;
//dd($data);
        if ($type == 'pdf') {
            $pdf = PDF::loadView('report.bank.cashbook.export', $data);
            $fileName = 'cashbook' . now()->format('Y-m-d H:i:s') . '.pdf';
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream($fileName,);
        }

        if ($type == 'excel') {
            $fileName = 'cashbook' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new CashbookReportExport($data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function holdItemsReport() {

        if (\request()->ajax()) {

            $account_id = \request()->input('account_id');

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'hold')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('lots.account_id', $account_id);
            }

            $items = $lotReturnsQuery->get();

            return view("report.bank.hold.table", compact('items'));
        }


        $accounts = Account::getAccounts();

        return view("report.bank.hold.report", compact('accounts'));
    }

    function bankHoldReportExport() {

        $account_id = \request()->input('account_id');

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'hold')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }

        $items = $lotReturnsQuery->get();

        $data['items'] = $items;

        $type = \request()->input('type');

        if ($type == 'pdf') {

            $pdf = PDF::loadView('report.bank.hold.export', $data);
            $fileName = 'hold-report' . now()->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName);
        }

        if ($type == 'excel') {
            $fileName = 'hold-report' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.hold.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function pendingItemsReport() {

        if (\request()->ajax()) {

            $account_id = \request()->input('account_id');

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'processing')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('lots.account_id', $account_id);
            }

            $items = $lotReturnsQuery->get();

            return view("report.bank.pending.table", compact('items'));
        }

        $accounts = Account::getAccounts();

        return view("report.bank.pending.report", compact('accounts'))->with(['financialYearRange' => $this->financialYearRange]);
    }

    function exportPendingItemsReport() {
        $account_id = \request()->input('account_id');

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'processing')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }

        $items = $lotReturnsQuery->get();

        $data['items'] = $items;

        $type = \request()->input('type');

        if ($type == 'pdf') {

            $pdf = PDF::loadView('report.bank.pending.export', $data);
            $fileName = 'pending-report_' . now()->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName);
        }

        if ($type == 'excel') {
            $fileName = 'pending_report_' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.pending.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function returnedItemsReport() {

        if (\request()->ajax()) {

            $account_id = \request()->input('account_id');

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'returned')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('lots.account_id', $account_id);
            }

            $items = $lotReturnsQuery->get();

            return view("report.bank.returned.table", compact('items'));
        }

        $accounts = Account::getAccounts();

        return view("report.bank.returned.report", compact('accounts'));
    }

    function exportReturnedItemsReport() {
        $account_id = \request()->input('account_id');

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'returned')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }

        $items = $lotReturnsQuery->get();

        $data['items'] = $items;

        $type = \request()->input('type');

        if ($type == 'pdf') {

            $pdf = PDF::loadView('report.bank.returned.export', $data);
            $fileName = 'returned_report_' . now()->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName);
        }

        if ($type == 'excel') {
            $fileName = 'returned_report_' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.returned.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function bankWiseReport() {

        if (\request()->ajax()) {

            $date = \request()->input('date');

            $date = explode('~', $date);

            $start = $date[0];
            $end = $date[1];

            $items = Account::with(['lotItems' => function ($query) use ($start, $end) {
                                    $query->whereBetween('lot_items.date', [$start, $end]);
                                }])
                            ->whereHas('lotItems', function ($query) use ($start, $end) {
                                $query->whereBetween('lot_items.date', [$start, $end]);
                            })->get();

            return view('report.bank-wise.table', compact('items'));
        }

        return view('report.bank-wise.report');
    }

    function exportBankWiseReport() {


        $date = \request()->input('date');

        $date = explode('~', $date);

        $start = $date[0];
        $end = $date[1];

        $items = Account::with(['lotItems' => function ($query) use ($start, $end) {
                        $query->whereBetween('lot_items.date', [$start, $end]);
                    }])
                ->whereHas('lotItems', function ($query) use ($start, $end) {
                    $query->whereBetween('lot_items.date', [$start, $end]);
                })
                ->get();

        $data['items'] = $items;
        $data['start'] = $start;
        $data['end'] = $end;

        $type = \request()->input('type');

        if ($type == 'pdf') {

            $pdf = PDF::loadView('report.bank-wise.export', $data);
            $fileName = 'hold-report' . now()->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName);
        }

        if ($type == 'excel') {
            $fileName = 'hold-report' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank-wise.export', $data), $fileName);
        }
        return "invalid export type";
    }

    function lotWiseReport() {

        if (\request()->ajax()) {

            $date = \request()->input('date');

            $date = explode('~', $date);

            $start = $date[0];
            $end = $date[1];

            $lots = Lot::with(['account', 'items'])
                    ->whereBetween('date', $date)
                    ->get();

            return view('report.lot-wise.table', compact('lots'));
        }

        return view('report.lot-wise.report');
    }

    function exportLotWiseReport() {

        $date = \request()->input('date');

        if (empty($date)) {
            return "Date Range is empty or invalid";
        }

        $date = explode('~', $date);

        $start = $date[0];
        $end = $date[1];

        $lots = Lot::with(['account', 'items'])
                ->whereBetween('date', $date)
                ->get();

        $type = \request()->input('type');

        $data['items'] = $lots;
        $data['start'] = $start;
        $data['end'] = $end;

        if ($type == 'pdf') {

            $pdf = PDF::loadView('report.lot-wise.export', $data)
                    ->setPaper('a4', 'landscape');
            $fileName = 'hold-report' . now()->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName,);
        }

        if ($type == 'excel') {
            $fileName = 'hold-report' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.lot-wise.export', $data), $fileName);
        }

        return view('report.lot-wise.export', compact('lots', 'start', 'end'));
    }

    function reconciliationReport() {

        $accounts = Account::getAccounts();

        if (\request()->ajax()) {
            $date = \request()->input('date');

            $account_id = \request()->input('account_id');

            $query = Transaction::with('account')->join('lot_items', 'lot_items.id', '=', 'transactions.lot_item_id')
                    ->join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->whereNotNull('lot_item_id')
                    ->select('transactions.id', 'transactions.date', 'transactions.amount', 'lots.lot_number', 'lot_items.index');

            if (!empty($account_id)) {
                $query->where('transactions.account_id', $account_id);
            }

            $dateRange = explode('~', $date);
            $query->whereBetween('transactions.date', [$dateRange[0], $dateRange[1]]);
            $lotTableData = $query->get();

            //before balance

            $balanceUntilDate = Transaction::
                            where('account_id', $account_id)
                            ->select([
                                DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                            ->whereDate('date', '<=', $dateRange[1])
                            ->where('status', 'final')
                            ->orderBy('date')
                            ->first()->balance;

            $bankCharge = Transaction::
                            //where('account_id', $accountId)
                            select([
                                DB::raw('SUM(amount) as balance')])
                            ->whereBetween('date', $dateRange)
                            ->where('status', 'final')
                            ->where('type', 'service-charge')
                            ->where('account_type', 'debit')
                            ->orderBy('date')
                            ->first()->balance;

            //all debit except service charge and lot items
            $totalExpense = Transaction::
                            //where('account_id', $accountId)
                            select([
                                DB::raw('SUM(amount) as balance')])
                            ->whereBetween('date', $dateRange)
                            ->where('status', 'final')
                            ->where('type', '!', 'service-charge')
                            ->whereNull('lot_item_id')
                            ->where('account_type', 'debit')
                            ->orderBy('date')
                            ->first()->balance;

            //Lot return

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->whereIn('lot_items.status', ['returned', 'processing'])
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('lots.account_id', $account_id);
            }

            $transactionQuery = Transaction::orderBy('date')
                    ->whereBetween('date', $dateRange)
                    ->where('status', 'final');

            if (!empty($account_id)) {
                $transactionQuery->where('transactions.account_id', $account_id);
            }

            $lotPending = $lotReturnsQuery->get();

            $unpaidAmount = $lotPending->sum('amount');

            $transactions = $transactionQuery->get();

            $hold = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'hold')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                    ->get();

            $accountBook = $lotTableData;

            $data['balanceUntilDate'] = $balanceUntilDate;
            $data['unpaidAmount'] = $unpaidAmount;
            $data['totalExpense'] = $totalExpense;
            $data['holdAmount'] = $hold->sum('amount');
            $data['start'] = $dateRange[0];
            $data['end'] = $dateRange[1];

            return view('report.bank.reconciliation.table', $data);
        }

        return view('report.bank.reconciliation.report', compact('accounts'));
    }

    function exportReconciliationReport() {

        $date = \request()->input('date');

        $accountId = \request()->input('account');

        $dateRange = explode('~', $date);

//        $account = Account::findOrFail($accountId);
        $account = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', '=', $accountId)
                ->first();
//        dd($account->bankName);
        //before balance

        $balanceUntilDate = Transaction::
                        where('account_id', $accountId)
                        ->select([
                            DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                        ->whereDate('date', '<=', $dateRange[1])
                        ->where('status', 'final')
                        ->orderBy('date')
                        ->first()->balance;

        //all debit except service charge and lot items
        $totalExpense = Transaction::
                        where('account_id', $accountId)
                        ->select([
                            DB::raw('SUM(amount) as balance')])
                        ->whereBetween('date', $dateRange)
                        ->where('status', 'final')
                        ->where('type', '!', 'service-charge')
                        ->whereNull('lot_item_id')
                        ->where('account_type', 'debit')
                        ->orderBy('date')
                        ->first()->balance;

        //Lot return

        $totalPending = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->whereIn('lot_items.status', ['returned', 'processing'])
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $totalPending->where('lots.account_id', $account_id);
        }

        $lotPending = $totalPending->get();

        $unpaidAmount = $lotPending->sum('amount');

        $hold = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'hold')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->get();

        $data['balanceUntilDate'] = $balanceUntilDate;
        $data['unpaidAmount'] = $unpaidAmount;
        $data['totalExpense'] = $totalExpense;
        $data['holdAmount'] = $hold->sum('amount');
        $data['account'] = $account;

        $type = \request()->input('type');

        $data['items'] = [];
        $data['start'] = $dateRange[0];
        $data['end'] = $dateRange[1];

        if ($type == 'pdf') {

            $pdf = PDF::loadView('report.bank.reconciliation.export', $data);
            $fileName = 'bank_reconciliation_' . now()->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName,);
        }

        if ($type == 'excel') {
            $fileName = 'bank_reconciliation_' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.reconciliation.export', $data), $fileName);
        }

        return "Export type is invalid";
    }

}
