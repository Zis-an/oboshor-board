<?php

namespace App\Http\Controllers;

use App\Exports\CashbookReportExport;
use App\Exports\ReportExport;
use App\Models\Account;
use App\Models\BudgetItem;
use App\Models\Cheque;
use App\Models\FinancialYear;
use App\Models\Head;
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
use Mpdf\Mpdf;

class ReportController extends ParentController {

    private $financialYear;
    private $financialYearRange;

    function __construct() {
        ini_set('memory_limit', '2048M');
        ini_set("pcre.backtrack_limit", "5000000");
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
            $head_id = request()->input('head_id');
            $head_item_id = request()->input('head_item_id');

            $dateRange = explode('~', $date);

            $incomes = Transaction::leftJoin("head_items", 'head_items.id', '=', 'transactions.head_item_id')
                    ->where("account_type", 'credit')
                    ->whereNotNull("transactions.head_id")
                    ->where('status', 'final')
                    ->whereBetween('date', $dateRange)
                    ->select('transactions.*', 'head_items.name as item_name')
                    ->orderBy('date', 'desc');

            if (!empty($head_id)) {
                $incomes->where('transactions.head_id', $head_id);
            }

            if (!empty($head_item_id)) {
                $incomes->where("transactions.head_item_id", $head_item_id);
            }

            $incomes = $incomes->get();

            $total = $incomes->sum('amount');

            return view("report.income.table", compact('incomes', 'total'));

            /* return \Yajra\DataTables\Facades\DataTables::of($incomes)
              ->addColumn('date', function ($row){
              return Carbon::parse($row->date)->format('d/m/Y');
              })
              ->editColumn('amount', function ($row){
              return number_format($row->amount, 2);
              })
              ->addIndexColumn()
              ->make(true); */
        }

        $incomeHeads = Head::where("type", 'income')
                ->pluck('name', 'id');

        return view('report.income.report', compact('incomeHeads'));
    }

    function incomeSubDetails($type, $acc_id, $dateRange) {
        $accounts = '';
        $banks = Account::select('accounts.*', 'banks.short', 'branches.name AS br_name')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', $acc_id)
                ->first();
        if ($type == 'te_six') {
            $accounts = Transaction::where('account_id', $acc_id)
                    ->where('status', 'final')
                    ->where('account_type', 'credit')
                    ->where('head_id', 7)
                    ->where('head_item_id', 110)
                    ->when($dateRange, function ($query) use ($dateRange) {
                        // Split the date range into fromDate and toDate
                        [$fromDate, $toDate] = explode('~', $dateRange);

                        // Apply the date range filter
                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                    })
                    ->get();
        }
        if ($type == 'others') {
            $accounts = Transaction::where('account_id', $acc_id)
                    ->where('status', 'final')
                    ->where('account_type', 'credit')
                    ->where('type', 'transfer_from')
                    ->when($dateRange, function ($query) use ($dateRange) {
                        // Split the date range into fromDate and toDate
                        [$fromDate, $toDate] = explode('~', $dateRange);

                        // Apply the date range filter
                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                    })
                    ->get();
        }


        if ($type == 'seventyfive') {
            $accounts = Transaction::where('account_id', $acc_id)
                    ->where('head_id', 4)
                    ->where('head_item_id', 107)
                    ->where('status', 'final')
                    ->where('account_type', 'credit')
                    ->when($dateRange, function ($query) use ($dateRange) {
                        // Split the date range into fromDate and toDate
                        [$fromDate, $toDate] = explode('~', $dateRange);

                        // Apply the date range filter
                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                    })
                    ->get();
        }

        if ($type == 'govt') {
            $accounts = Transaction::where('account_id', $acc_id)
                    ->where('head_id', 8)
                    ->where('status', 'final')
                    ->where('account_type', 'credit')
                    ->when($dateRange, function ($query) use ($dateRange) {
                        // Split the date range into fromDate and toDate
                        [$fromDate, $toDate] = explode('~', $dateRange);

                        // Apply the date range filter
                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                    })
                    ->get();
        }

        if ($type == 'return') {
            $accounts = Transaction::where('account_id', $acc_id)
                    ->where('status', 'final')
                    ->where('account_type', 'credit')
                    ->where('type', 'returned')
                    ->when($dateRange, function ($query) use ($dateRange) {
                        // Split the date range into fromDate and toDate
                        [$fromDate, $toDate] = explode('~', $dateRange);

                        // Apply the date range filter
                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                    })
                    ->get();
        }
        return view('report.std-account.incomesubdetails', compact('accounts', 'banks'));
    }

    function incomeReportExport() {


        $date = \request()->input('date');
        $head_id = request()->input('head_id');
        $head_item_id = request()->input('head_item_id');

        $dateRange = explode('~', $date);

        $incomes = Transaction::leftJoin("head_items", 'head_items.id', '=', 'transactions.head_item_id')
                ->leftJoin('accounts', 'accounts.id', '=', 'transactions.account_id')
                ->where("account_type", 'credit')
                ->whereNotNull("transactions.head_id")
                ->where('status', 'final')
                ->whereBetween('date', $dateRange)
                ->select('transactions.*', 'head_items.name as item_name', 'accounts.account_no AS bankAccount')
                ->orderBy('date', 'desc');

        $head_item = '';
        $head_sub_iteem = '';
        if (!empty($head_id)) {
            $incomes->where('transactions.head_id', $head_id);
            $head_item = Head::where('id', $head_id)->first();
        }

        if (!empty($head_item_id)) {
            $incomes->where("transactions.head_item_id", $head_item_id);
            $head_sub_iteem = \App\Models\HeadItem::where('id', $head_item_id)->first();
        }

        $incomes = $incomes->get();

        $total = $incomes->sum('amount');

        $data['incomes'] = $incomes;
        $data['total'] = $total;
        $data['dateRange'] = $dateRange;
        $data['head_item'] = $head_item;
        $data['head_sub_item'] = $head_sub_iteem;

        $type = \request()->input("type");

        if ($type == 'pdf') {
//            $pdf = PDF::loadView('report.income.export', $data);
//            $fileName = 'income_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf';
//            return $pdf->stream($fileName,);


            $html = view('report.income.export', $data);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
            'solaimanlipi' => [
                'R' => 'fonts/SolaimanLipi.ttf',
                'I' => 'fonts/SolaimanLipi.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
                ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'orientation' => 'L',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch'
            ]);

            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . \Carbon\Carbon::now('Asia/Dhaka')->format('d/m/Y h:i A') . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('income_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = '6percent_income_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d_H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.income.export', $data), $fileName, \Maatwebsite\Excel\Excel::XLSX);
        }

        return "<h4>this export type is not supported</h4>";

        return view("report.income.table", compact('incomes', 'total'));
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

        $query = '';

        $export = \request()->query('export', false);

        if (\request()->ajax() || $export) {

            $date = \request()->input('date');

            $dateRange = explode('~', $date);

            $query = Transaction::with(['transactionFor', 'expenseItems' => function ($query) {
                            $query->with('head', 'headItem');
                        }])
                    ->where('type', 'expense')
                    ->whereDate('date', '>=', $dateRange[0])
                    ->whereDate('date', '<=', $dateRange[1])
                    ->orderBy('date');

            //if exportable


            if ($export) {

                $transactions = $query->get();

                $data['transactions'] = $transactions;
                $data['start'] = $dateRange[0];
                $data['end'] = $dateRange[1];
                $data['total'] = $transactions->sum('amount');

                $type = \request()->query('type');

                return $this->handleExport('report.expense-report-export', $type, $data, 'expense_report', 'L');
            }

            return DataTables::of($query)
                            ->addColumn('for', function ($row) {
                                return $row->transactionFor->name ?? '';
                            })
                            ->addColumn('title', function ($row) {
                                if (!empty($row->expenseItems)) {
                                    $headName = $row->expenseItems->pluck('head.name')->join(', ');
                                    $headItem = $row->expenseItems->pluck('headItem.name')->join(',');

                                    return $headItem ?? $headName;
                                }

                                return '';
                            })
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
                    ->with(["head", 'headItem', 'expenseItems' => function ($query) {
                            $query->with('head', 'headItem');
                        }])
                    ->leftJoin('lot_items', 'lot_items.id', '=', 'transactions.lot_item_id')
                    ->leftJoin('lots', 'lots.id', '=', 'lot_items.lot_id')
//                    ->whereBetween('transactions.date', $dateRange)
                    ->whereDate('transactions.date', '>=', $dateRange[0])
                    ->whereDate('transactions.date', '<=', $dateRange[1])
                    ->where('transactions.status', 'final')
                    ->select('transactions.id', 'transactions.date',
                    'transactions.amount', 'transactions.account_type',
                    'transactions.lot_item_id',
                    'transactions.head_id',
                    'transactions.head_item_id',
                    'lot_items.index', 'lots.name as lot_name', 'lots.short_name as short_name', 'lots.file_page as file_page', 'transactions.description',
            );

            if (!empty($account_id)) {
                $transactionQuery->where('transactions.account_id', $account_id);
            }

            $transactions = $transactionQuery->get();

            $accounts_details = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                    ->leftJoin('banks', 'banks.id', '=', 'accounts.bank_id')
                    ->leftJoin('branches', 'branches.id', '=', 'accounts.branch_id')
                    ->where('accounts.id', $account_id)
                    ->first();

            return view("report.bank.cashbook.table", compact('transactions', 'openingBalance', 'start', 'end', 'accounts_details'));
        }

        $accounts = Account::getAccounts();

        $pettyCashAccount = Account::where('is_cash_account', true)
                ->first();

        return view('report.bank.cashbook.report', compact('accounts', 'pettyCashAccount')
                )->with(['financialYearRange' => $this->financialYearRange]);
    }

    function bankCashBookReportExportPDF() {
        $date = \request()->input('date');

        $dateRange = explode('~', $date);

        $start = $dateRange[0];

        $end = $dateRange[1];

        $account_id = \request()->input('account');

        $accounts_details = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->leftJoin('banks', 'banks.id', '=', 'accounts.bank_id')
                ->leftJoin('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', $account_id)
                ->first();

        $openingBalance = Transaction::
                        where('account_id', $account_id)
                        ->select([
                            DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                        ->whereDate('date', '<', $dateRange[0])
                        ->where('status', 'final')
                        ->orderBy('date')
                        ->first()->balance;

        $transactionQuery = Transaction::orderBy('date')
                ->with(["head", 'headItem', 'expenseItems' => function ($query) {
                        $query->with('head', 'headItem');
                    }])
                ->leftJoin('lot_items', 'lot_items.id', '=', 'transactions.lot_item_id')
                ->leftJoin('lots', 'lots.id', '=', 'lot_items.lot_id')
//                ->whereBetween('transactions.date', $dateRange)
                ->whereDate('transactions.date', '>=', $start)
                ->whereDate('transactions.date', '<=', $end)
                ->where('transactions.status', 'final')
                ->select('transactions.id', 'transactions.date',
                'transactions.amount', 'transactions.account_type',
                'transactions.lot_item_id',
                'transactions.head_id', 'transactions.head_item_id',
                'transactions.type',
                'lot_items.index', 'lots.name as lot_name', 'lots.short_name as short_name', 'lots.file_page as file_page', 'transactions.description',
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
        $data['accounts_details'] = $accounts_details;

//dd($data);
        if ($type == 'pdf') {
//
            $html = view('report.bank.cashbook.export', $data);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
            'solaimanlipi' => [
                'R' => 'fonts/SolaimanLipi.ttf',
                'I' => 'fonts/SolaimanLipi.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
                ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch',
                'orientation' => 'L',
            ]);
            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . \Carbon\Carbon::now('Asia/Dhaka')->format('d/m/Y h:i A') . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('cashbook' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = 'cashbook' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new CashbookReportExport($data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function holdItemsReport() {

        if (\request()->ajax()) {

            $account_id = \request()->input('account_id');
            $financialYearId = \request()->input('financial_year_id', null);
            if (isset($financialYearId) && !empty($financialYearId)) {
                $financialYear = FinancialYear::findOrFail($financialYearId);
            }

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'hold')
                    ->select('lots.name as lot_name', 'lots.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('lots.account_id', $account_id);
            }
            if (isset($financialYearId) && !empty($financialYear)) {
                $lotReturnsQuery->whereBetween("lots.date", [$financialYear->start_date, $financialYear->end_date . ' 23:59:59']);
            }

            $items = $lotReturnsQuery->get();

            return view("report.bank.hold.table", compact('items'));
        }


        $accounts = Account::getAccounts();
        $financialYears = FinancialYear::getForDropdown();
        return view("report.bank.hold.report", compact('accounts', 'financialYears'));
    }

    function stopItemsReport() {

        if (\request()->ajax()) {

            $account_id = \request()->input('account_id');

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'stop')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('lots.account_id', $account_id);
            }

            $items = $lotReturnsQuery->get();

            return view("report.bank.stop.table", compact('items'));
        }


        $accounts = Account::getAccounts();

        return view("report.bank.stop.report", compact('accounts'));
    }

    function bankHoldReportExport() {

        $account_id = \request()->input('account');
        
        $financialYearId = \request()->input('fisc_year_id', null);
            if (isset($financialYearId) && !empty($financialYearId)) {
                $financialYear = FinancialYear::findOrFail($financialYearId);
            }

        $account = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', '=', $account_id)
                ->first();

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'hold')
                ->select('lots.name as lot_name', 'lots.date as lot_date', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }
        if (isset($financialYearId) && !empty($financialYear)) {
                $lotReturnsQuery->whereBetween("lots.date", [$financialYear->start_date, $financialYear->end_date . ' 23:59:59']);
            }

        $items = $lotReturnsQuery->get();

        $data['items'] = $items;
        $data['account'] = $account;

        $type = \request()->input('type');

        if ($type == 'pdf') {

            $html = view('report.bank.hold.export', $data);
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
            'solaimanlipi' => [
                'R' => 'fonts/SolaimanLipi.ttf',
                'I' => 'fonts/SolaimanLipi.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
                ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch'
            ]);
            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . date('d/m/Y H:i A', strtotime(now()->setTimezone('Asia/Dhaka'))) . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('stop-report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = 'hold-report' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.hold.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function exportStopItemsReport() {

        $account_id = \request()->input('account');
        $account = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', '=', $account_id)
                ->first();
//        dd($account_id);

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'stop')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }

        $items = $lotReturnsQuery->get();

        $data['items'] = $items;
        $data['account'] = $account;
//        dd($data);

        $type = \request()->input('type');
//        dd($type);

        if ($type == 'pdf') {
            $html = view('report.bank.stop.export', $data);
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
            'solaimanlipi' => [
                'R' => 'fonts/SolaimanLipi.ttf',
                'I' => 'fonts/SolaimanLipi.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
                ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch'
            ]);
            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . \Carbon\Carbon::now('Asia/Dhaka')->format('d/m/Y h:i A') . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('stop-report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = 'stop-report' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.hold.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function paymentItemsReport() {

        if (\request()->ajax()) {

            $account_id = \request()->input('account_id');
            $date = \request()->input('date');
            $dateRange = explode('~', $date);
            $start = $dateRange[0];

            $end = $dateRange[1];

//            $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
//                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
//                    ->whereDate('transactions.date', '>=', '2022-07-01')
//                    ->whereDate('transactions.date', '<=', $dateRange[1])
//                    ->where('transactions.account_type', '=', 'credit')
//                    ->groupBy('transactions.lot_item_id');
//
//            if (!empty($account_id)) {
//                $lotTreanReturn->where('lots.account_id', $account_id);
//            }
//
//            $returnItems = $lotTreanReturn->pluck('lot_items.index')->toArray();
            
            $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0] . ' 00:00:00')
                ->whereDate('transactions.date', '<=', $dateRange[1] . ' 23:59:59')
                ->groupBy('transactions.lot_item_id')
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotTreanReturn->where('lots.account_id', $account_id);
        }

        $returnItems = $lotTreanReturn->pluck('lot_items.index')->toArray();

//            $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
//                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
//                    ->whereDate('transactions.date', '>=', $dateRange[0])
//                    ->whereDate('transactions.date', '<=', $dateRange[1])
//                    ->where('transactions.account_type', '=', 'debit')
//                    ->groupBy('transactions.lot_item_id')
////                    ->orderBy('transactions.date', 'ASC')
//                    ->select('lots.short_name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'transactions.date AS paymDate');
//
//            if (!empty($account_id)) {
//                $lotTreanQuery->where('lots.account_id', $account_id);
//            }

//            $prossItems = $lotTreanQuery->get();
            
            
            $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0])
                ->whereDate('transactions.date', '<=', $dateRange[1])
                ->where('transactions.account_type', '=', 'debit')
                ->groupBy('transactions.lot_item_id')
                ->select('lots.short_name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'transactions.date AS paymDate');

        if (!empty($account_id)) {
            $lotTreanQuery->where('lots.account_id', $account_id);
        }

        $prossItems = $lotTreanQuery->get();

            return view("report.bank.payment.table", compact('end', 'prossItems', 'returnItems', 'lotTreanReturn'));
        }

        $accounts = Account::getAccounts();

        return view("report.bank.payment.report", compact('accounts'))->with(['financialYearRange' => $this->financialYearRange]);
    }

    function exportPaymentItemsReport() {
        $account_id = \request()->input('account');
        $date = \request()->input('date');

        $dateRange = explode('~', $date);
        $start = $dateRange[0];

        $end = $dateRange[1];

        $account = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', '=', $account_id)
                ->first();

//        $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
//            ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
//            ->whereDate('transactions.date', '>=', $dateRange[0])
//            ->whereDate('transactions.date', '<=', $dateRange[1])
//            ->where('transactions.account_type', '=', 'credit')
//            ->groupBy('transactions.lot_item_id');
//
//        if (!empty($account_id)) {
//            $lotTreanReturn->where('lots.account_id', $account_id);
//        }
//
//        $returnItems = $lotTreanReturn->pluck('lot_items.index')->toArray();


        $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0] . ' 00:00:00')
                ->whereDate('transactions.date', '<=', $dateRange[1] . ' 23:59:59')
                ->groupBy('transactions.lot_item_id')
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotTreanReturn->where('lots.account_id', $account_id);
        }

        $returnItems = $lotTreanReturn->pluck('lot_items.index')->toArray();
//dd($lotTreanReturn->get());

        $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0] . ' 00:00:00')
                ->whereDate('transactions.date', '<=', $dateRange[1] . ' 23:59:59')
                ->where('transactions.account_type', '=', 'debit')
                ->groupBy('transactions.lot_item_id')
                ->select('lots.short_name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'transactions.date AS paymDate');

        if (!empty($account_id)) {
            $lotTreanQuery->where('lots.account_id', $account_id);
        }

        $prossItems = $lotTreanQuery->get();
//        dd($prossItems->count() - $lotTreanReturn->count());

        $data['prossItems'] = $prossItems;

        $data['end'] = $end;
        $data['start'] = $start;
        $data['account'] = $account;
        $data['returns'] = $returnItems;
        $data['paidItemtotal'] = $prossItems->count() - $lotTreanReturn->count();
        $data['paidItemAmount'] = $prossItems->sum('amount') - $lotTreanReturn->sum('amount');

        $type = \request()->input('type');

        if ($type == 'pdf') {

//            $pdf = PDF::loadView('report.bank.payment.export', $data);
//            $fileName = 'payment_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf';
//            return $pdf->stream($fileName);

            $html = view('report.bank.payment.export', $data);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
            'solaimanlipi' => [
                'R' => 'fonts/SolaimanLipi.ttf',
                'I' => 'fonts/SolaimanLipi.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
                ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch'
            ]);
            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . \Carbon\Carbon::now('Asia/Dhaka')->format('d/m/Y h:i A') . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('payment_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = 'payment_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.payment.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function pendingItemsReport() {

        if (\request()->ajax()) {

            $account_id = \request()->input('account_id');
            $date = \request()->input('date');

            $dateRange = explode('~', $date);
            $start = $dateRange[0];

            $end = $dateRange[1];

            $lotProssQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->where('lot_items.status', 'processing')
                    ->whereDate('lots.date', '>=', '2022-07-01')
                    ->whereDate('lots.date', '<=', $end)
                    ->select('lots.name as lot_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotProssQuery->where('lots.account_id', $account_id);
            }

            $prossItems = $lotProssQuery->get();

            $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->whereDate('lots.date', '<=', $end)
                    ->whereDate('transactions.date', '>', $end)
                    ->where('transactions.account_type', '=', 'debit')
                    ->groupBy('transactions.lot_item_id')
                    ->select('lots.name as lot_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotTreanQuery->where('lots.account_id', $account_id);
            }

            $transac = $lotTreanQuery->get();

            $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->groupBy('transactions.lot_item_id')
                    ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'lot_items.id as indexId')
                    ->havingRaw('count % 2 = 0');

            if (!empty($account_id)) {
                $lotReturnsQuery->where('lots.account_id', $account_id);
            }

            $items = $lotReturnsQuery->get();

            return view("report.bank.pending.table", compact('items', 'end', 'transac', 'prossItems'));
        }

        $accounts = Account::getAccounts();

        return view("report.bank.pending.report", compact('accounts'))->with(['financialYearRange' => $this->financialYearRange]);
    }

    function exportPendingItemsReport() {
        $account_id = \request()->input('account');
        $date = \request()->input('date');

        $dateRange = explode('~', $date);
        $start = $dateRange[0];

        $end = $dateRange[1];

        $account = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', '=', $account_id)
                ->first();

        //Pending data query
        $lotProssQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'processing')
                ->whereDate('lots.date', '>=', '2022-07-01')
                ->whereDate('lots.date', '<=', $end)
                ->select('lots.name as lot_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotProssQuery->where('lots.account_id', $account_id);
        }

        $prossItems = $lotProssQuery->get();
//            dd($date);
        $data['prossItems'] = $prossItems;

        $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('lots.date', '<=', $end)
                ->whereDate('transactions.date', '>', $end)
                ->where('transactions.account_type', '=', 'debit')
                ->groupBy('transactions.lot_item_id')
                ->select('lots.name as lot_name', 'lots.short_name as short_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotTreanQuery->where('lots.account_id', $account_id);
        }

        $transac = $lotTreanQuery->get();
        $data['transac'] = $transac;

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->groupBy('transactions.lot_item_id')
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'lot_items.id as indexId')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }

        $items = $lotReturnsQuery->get();

        $data['items'] = $items;
        $data['end'] = $end;
        $data['start'] = $start;
        $data['account'] = $account;

        $type = \request()->input('type');

        if ($type == 'pdf') {

            $html = view('report.bank.pending.export', $data);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
            'solaimanlipi' => [
                'R' => 'fonts/SolaimanLipi.ttf',
                'I' => 'fonts/SolaimanLipi.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
                ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch'
            ]);
            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . \Carbon\Carbon::now('Asia/Dhaka')->format('d/m/Y h:i A') . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('pending_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = 'pending_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.pending.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";
    }

    function returnedItemsReport() {

        if (\request()->ajax()) {
            $date = \request()->input('date');

            $dateRange = explode('~', $date);

            $account_id = \request()->input('account_id');

            $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0])
                ->whereDate('transactions.date', '<=', $dateRange[1])
//                ->where('transactions.account_type', '=', 'credit')
                ->groupBy('transactions.lot_item_id')
//                ->select('lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotTreanReturn->where('lots.account_id', $account_id);
        }

        $items = $lotTreanReturn->get();

//            $stop = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
//                ->whereDate('lot_items.updated_at', '>=', '2022-07-01')
//                ->whereDate('lot_items.updated_at', '<=', $dateRange[1])
//                ->where('lot_items.status', 'stop')
//                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
//                ->get();
//
//        $stop_amount = $stop->sum('amount');
//        $stop_count = $stop->count();
//
//            $pend_stop_count = 0;
//        $pend_stop_amnt = 0;
//        $ret_stop_count = 0;
//        $ret_stop_amnt = 0;
//        $srop_data_arr = array();
//        if ($stop->count() > 0) {
//
//            foreach ($stop as $row) {
//                $ret_tran = Transaction::where('lot_item_id', '=', $row->lot_item_id)->count();
//                $srop_data_arr[] = $row->index;
//                if ($ret_tran > 0) {
//                    $ret_stop_count++;
//                    $ret_stop_amnt = $ret_stop_amnt + $row->amount;
//                } else {
//                    $pend_stop_count++;
//                    $pend_stop_amnt = $pend_stop_amnt + $row->amount;
//                }
//            }
//        }
//
//        $data['ret_stop_count'] = $ret_stop_count;
//        $data['ret_stop_amnt'] = $ret_stop_amnt;
//        $data['pend_stop_count'] = $pend_stop_count;
//        $data['pend_stop_amnt'] = $pend_stop_amnt;
//        $data['srop_data_arr'] = $srop_data_arr;

            return view("report.bank.returned.table", compact('items'));
        }

        $accounts = Account::getAccounts();

        return view("report.bank.returned.report", compact('accounts'))->with(['financialYearRange' => $this->financialYearRange]);
    }

    function exportReturnedItemsReport() {
        $date = \request()->input('date');

        $dateRange = explode('~', $date);
        $account_id = \request()->input('account');

        $start = $dateRange[0];

        $end = $dateRange[1];

        $account = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', '=', $account_id)
                ->first();

//        dd($account_id);

        $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0])
                ->whereDate('transactions.date', '<=', $dateRange[1])
//                ->where('transactions.account_type', '=', 'credit')
                ->groupBy('transactions.lot_item_id')
//                ->select('lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotTreanReturn->where('lots.account_id', $account_id);
        }

        $items = $lotTreanReturn->get();

        $stop = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->whereDate('lot_items.updated_at', '>=', '2022-07-01')
                ->whereDate('lot_items.updated_at', '<=', $dateRange[1])
                ->where('lot_items.status', 'stop')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->get();

        $stop_amount = $stop->sum('amount');
        $stop_count = $stop->count();

        $pend_stop_count = 0;
        $pend_stop_amnt = 0;
        $ret_stop_count = 0;
        $ret_stop_amnt = 0;
        $srop_data_arr = array();
        if ($stop->count() > 0) {

            foreach ($stop as $row) {
                $ret_tran = Transaction::where('lot_item_id', '=', $row->lot_item_id)->count();
                $srop_data_arr[] = $row->index;
                if ($ret_tran > 0) {
                    $ret_stop_count++;
                    $ret_stop_amnt = $ret_stop_amnt + $row->amount;
                } else {
                    $pend_stop_count++;
                    $pend_stop_amnt = $pend_stop_amnt + $row->amount;
                }
            }
        }

        $data['ret_stop_count'] = $ret_stop_count;
        $data['ret_stop_amnt'] = $ret_stop_amnt;
        $data['pend_stop_count'] = $pend_stop_count;
        $data['pend_stop_amnt'] = $pend_stop_amnt;
        $data['srop_data_arr'] = $srop_data_arr;

        $data['end'] = $end;
        $data['start'] = $start;
        $data['items'] = $items;
        $data['account'] = $account;

        $type = \request()->input('type');

        if ($type == 'pdf') {

//            $pdf = PDF::loadView('report.bank.returned.export', $data);
//            $fileName = 'returned_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf';
//            return $pdf->stream($fileName);

            $html = view('report.bank.returned.export', $data);

            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    public_path(),
                ]),
                'fontdata' => $fontData + [
            'solaimanlipi' => [
                'R' => 'fonts/SolaimanLipi.ttf',
                'I' => 'fonts/SolaimanLipi.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
                ],
                'default_font' => 'sans-serif',
                'mode' => 'utf-8',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch'
            ]);
            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . date('d/m/Y H:i A', strtotime(now()->setTimezone('Asia/Dhaka'))) . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('returned_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = 'returned_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
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
            $fileName = 'hold-report' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName);
        }

        if ($type == 'excel') {
            $fileName = 'hold-report' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank-wise.export', $data), $fileName);
        }
        return "invalid export type";
    }

    function lotWiseReport() {


        $export = \request()->query('export');
        $type = \request()->query('type');

        if (\request()->ajax() || $export) {

            $accountId = \request()->input('account_id');
            $account = null;

            $query = Lot::with(['account', 'items']);

            if ($accountId) {
                $query->where('account_id', $accountId);
                $account = Account::find($accountId);
            }

            $lots = $query->get();

            $data['lots'] = $lots;
            $data['account'] = $account;

            if ($export) {
                $this->handleExport('report.lot-wise.export', $type, $data, 'lot_report', 'L');
            }

            return view('report.lot-wise.table', compact('lots'));
        }

        $accounts = Account::getAccounts();

        return view('report.lot-wise.report', compact('accounts'));
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
            $fileName = 'hold-report' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName,);
        }

        if ($type == 'excel') {
            $fileName = 'hold-report' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
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

            //pending Amount start
            $total_pending_amount = 0;
            $lotProssQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->where('lot_items.status', 'processing')
                    ->whereDate('lots.date', '>=', '2022-07-01')
                    ->whereDate('lots.date', '<=', $dateRange[1])
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotProssQuery->where('lots.account_id', $account_id);
            }

            $prossItems = $lotProssQuery->get();
            $processing_amount = $prossItems->sum('amount');
            $processing_count = $prossItems->count();

            $total_pending_amount = $total_pending_amount + $prossItems->sum('amount');
            $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->whereDate('transactions.date', '>=', $dateRange[0])
                    ->whereDate('transactions.date', '<=', $dateRange[1])
                    ->where('transactions.account_type', '=', 'debit')
                    ->groupBy('transactions.lot_item_id')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotTreanQuery->where('lots.account_id', $account_id);
            }

            $transac = $lotTreanQuery->get();
            $paid_amount = $transac->sum('amount');
            $paid_count = $transac->count();
//            $total_pending_amount = $total_pending_amount + $transac->sum('amount');


            $lotTreanPending = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->whereDate('lots.date', '<=', $dateRange[1])
                    ->whereDate('transactions.date', '>', $dateRange[1])
                    ->where('transactions.account_type', '=', 'debit')
                    ->groupBy('transactions.lot_item_id')
                    ->select('lots.name as lot_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

            if (!empty($account_id)) {
                $lotTreanPending->where('lots.account_id', $account_id);
            }

            $transacPending = $lotTreanPending->get();

            $tranpendAmount = $transacPending->sum('amount');
            $tranpendCount = $transacPending->count();

            $lotOtvutQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->groupBy('transactions.lot_item_id')
                    ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'lot_items.id as indexId', 'transactions.account_type AS TranType', 'transactions.date AS TranDate', 'transactions.id AS TranID')
                    ->havingRaw('count % 2 = 0');

            if (!empty($account_id)) {
                $lotOtvutQuery->where('lots.account_id', $account_id);
            }

            $otvutItems = $lotOtvutQuery->get();
//            return $otvutItems->count();
            $return_amount = 0;
            $return_count = 0;
            foreach ($otvutItems as $key => $raw) {

                if ($raw->date <= $dateRange[1]) {
                    $item = Transaction::where('lot_item_id', $raw->indexId)->orderBy('id', 'DESC')->first();

                    if ($item->account_type == 'credit') {
                        $return_count++;
                        $total_pending_amount = $total_pending_amount + $raw->amount;
                        $return_amount = $return_amount + $raw->amount;
                    }
                }
//                $date_data .= $otvutItems->count() . '+++++' . $raw->TranType . '---' . $raw->TranDate . '===' . $dateRange[1] . '<br />';
            }
//            return $otvutItems->count();
//            return $return_amount;

            $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                    ->whereDate('transactions.date', '>=', '2022-07-01')
                    ->whereDate('transactions.date', '<=', $dateRange[1])
//                    ->where('transactions.account_type', '=', 'credit')
                    ->groupBy('transactions.lot_item_id')
//                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');
                    ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                    ->havingRaw('count % 2 = 0');

            if (!empty($account_id)) {
                $lotTreanReturn->where('lots.account_id', $account_id);
            }

            $returnDateItems = $lotTreanReturn->get();

            //Pending amount end

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
                    ->whereDate('lots.date', '>=', $dateRange[0])
                    ->whereDate('lots.date', '<=', $dateRange[1])
                    ->where('lot_items.status', 'hold')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                    ->get();

            $hold_amount = $hold->sum('amount');
            $hold_count = $hold->count();

            $stop = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                    ->whereDate('lot_items.updated_at', '>=', '2022-07-01')
                    ->whereDate('lot_items.updated_at', '<=', $dateRange[1])
                    ->where('lot_items.status', 'stop')
                    ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'lot_items.id AS lot_item_id')
                    ->get();

            $stop_amount = $stop->sum('amount');
            $stop_count = $stop->count();

            $pend_stop_count = 0;
            $pend_stop_amnt = 0;
            $ret_stop_count = 0;
            $ret_stop_amnt = 0;
            if ($stop->count() > 0) {

                foreach ($stop as $row) {
                    $ret_tran = Transaction::where('lot_item_id', '=', $row->lot_item_id)->count();
                    if ($ret_tran > 0) {
                        $ret_stop_count++;
                        $ret_stop_amnt = $ret_stop_amnt + $row->amount;
                    } else {
                        $pend_stop_count++;
                        $pend_stop_amnt = $pend_stop_amnt + $row->amount;
                    }
                }
            }


//            return $ret_stop_count;

            $accountBook = $lotTableData;

            $data['ret_stop_count'] = $ret_stop_count;
            $data['ret_stop_amnt'] = $ret_stop_amnt;
            $data['pend_stop_count'] = $pend_stop_count;
            $data['pend_stop_amnt'] = $pend_stop_amnt;

            $data['balanceUntilDate'] = $balanceUntilDate;
            $data['unpaidAmount'] = $total_pending_amount;
            $data['pending_amount'] = $processing_amount;
            $data['return_amount'] = $return_amount;
            $data['totalExpense'] = $totalExpense;
            $data['holdAmount'] = $hold->sum('amount');
            $data['start'] = $dateRange[0];
            $data['end'] = $dateRange[1];
            $data['return_count'] = $return_count;
            $data['pending_count'] = $processing_count;
            $data['paid_amount'] = $paid_amount;
            $data['paid_count'] = $paid_count;
            $data['hold_amount'] = $hold_amount;
            $data['hold_count'] = $hold_count;
            $data['stop_amount'] = $stop_amount;
            $data['stop_count'] = $stop_count;
            $data['tranpendAmount'] = $tranpendAmount;
            $data['tranpendCount'] = $tranpendCount;
            $data['returnDateItems'] = $returnDateItems;

            return view('report.bank.reconciliation.table', $data);
        }

        return view('report.bank.reconciliation.report', compact('accounts'));
    }

    function exportReconciliationReport() {

        $date = \request()->input('date');

        $accountId = \request()->input('account');
        $account_id = $accountId;

        $dateRange = explode('~', $date);
        $start = $dateRange[0];
        $end = $dateRange[1];

//        $account = Account::findOrFail($accountId);
        $account = Account::select('accounts.*', 'banks.name AS bankName', 'branches.name AS branchName')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->join('branches', 'branches.id', '=', 'accounts.branch_id')
                ->where('accounts.id', '=', $accountId)
                ->first();
//        dd($account);
        //before balance

        $opening_bal = Transaction::where('account_id', $accountId)->where('type', '=', 'opening_balance')->first();
//            dd($opening_bal->amount);
//        if (isset($opening_bal->amount) && !empty($opening_bal->amount)) {
//            $balanceOpendate = $opening_bal->amount;
//        } else {
//            $balanceOpendate = Transaction::where('account_id', $accountId)
//                ->select([
//                    DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
//                ->whereDate('date', '<', $dateRange[0])
//                ->where('status', 'final')
//                ->orderBy('date')
//                ->first()->balance;
//        }


        $openingBalance = Transaction::where('account_id', $accountId)->where('type', '=', 'opening_balance')
                ->first();
        $balanceOpendate = Transaction::where('account_id', $accountId)
                        ->select([
                            DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                        ->when(!empty($openingBalance), function ($q) use ($openingBalance) {
                            return $q->whereDate('date', '>=', $openingBalance->date);
                        })
                        //->whereDate('date', '>=', $openingBalance->date)
                        ->whereDate('date', '<', $start)
                        ->where('status', 'final')
                        ->orderBy('date')
                        ->first()->balance;

//        dd($balanceOpendate);

        $credit_bal = Transaction::where('account_id', $accountId)
                ->whereDate('date', '>=', $dateRange[0])
                ->whereDate('date', '<=', $dateRange[1])
                ->where('account_type', 'credit')
                ->where('status', 'final')
                ->sum('amount');

        $debit_bal = Transaction::where('account_id', $accountId)
                ->whereDate('date', '>=', $dateRange[0])
                ->whereDate('date', '<=', $dateRange[1])
                ->where('account_type', 'debit')
                ->where('status', 'final')
                ->sum('amount');
        if (isset($opening_bal->amount) && !empty($opening_bal->amount)) {
            $upto_bal = $balanceOpendate + $credit_bal - $debit_bal + $opening_bal->amount;
        } else {
            $upto_bal = $balanceOpendate + $credit_bal - $debit_bal;
        }
//        dd($upto_bal);
        $balanceUntilDate = Transaction::
                        where('account_id', $accountId)
                        ->select([
                            DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                        ->whereDate('date', '<=', $dateRange[0])
                        ->whereDate('date', '<=', $dateRange[1])
                        ->where('status', 'final')
                        ->orderBy('date')
                        ->first()->balance;

//        dd($balanceUntilDate);
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

        $cashAccount = Account::where('is_cash_account', 1)->first();

        $cashbookAmount = Transaction::
                        where('account_id', $cashAccount->id)
                        ->select([
                            DB::raw('SUM(IF(transactions.account_type="credit", amount, -1 * amount)) as balance')])
                        //->whereDate('date', '<=', $dateRange[0])
                        ->whereDate('date', '<=', $dateRange[1])
                        ->where('status', 'final')
                        ->orderBy('date')
                        ->first()->balance;

        $unpaidChequeAmount = //$transactions->whereHas('cheques', function ($query) use ($end) {
                Cheque::where('cheques.type', 'transaction')
                ->where('account_id', $accountId)
                ->whereDate('cheques.issue_date', '<=', $end)
                ->whereDate('cheques.issue_date', '>=', $start)
                ->where(function ($q) use ($end) {
                    $q->whereDate('cheques.transaction_completed_date', '>', $end)
                    ->orWhere('cheques.transaction_completed_date', null);
                })
                ->sum('amount');

        //Lot return

        $totalPending = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->whereIn('lot_items.status', ['returned', 'processing'])
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($accountId)) {
            $totalPending->where('lots.account_id', $accountId);
        }

        $lotPending = $totalPending->get();

        $unpaidAmount = $lotPending->sum('amount');

//pending Amount start
        $total_pending_amount = 0;
        $lotProssQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->where('lot_items.status', 'processing')
                ->whereDate('lots.date', '>=', '2022-07-01')
                ->whereDate('lots.date', '<=', $dateRange[1])
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotProssQuery->where('lots.account_id', $account_id);
        }

        $prossItems = $lotProssQuery->get();
//        dd($prossItems);
        $processing_amount = $prossItems->sum('amount');
        $processing_count = $prossItems->count();
//        dd($processing_count);

        $total_pending_amount = $total_pending_amount + $prossItems->sum('amount');

        $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0])
                ->whereDate('transactions.date', '<=', $dateRange[1])
                ->where('transactions.account_type', '=', 'debit')
                ->groupBy('transactions.lot_item_id')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotTreanQuery->where('lots.account_id', $account_id);
        }

        $transac = $lotTreanQuery->get();
//        dd($transac);
        $paid_amount = $transac->sum('amount');
        $paid_count = $transac->count();
        $total_pending_amount = $total_pending_amount + $transac->sum('amount');

        $lotTreanPending = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('lots.date', '<=', $dateRange[1])
                ->whereDate('transactions.date', '>', $dateRange[1])
                ->where('transactions.account_type', '=', 'debit')
                ->groupBy('transactions.lot_item_id')
                ->select('lots.name as lot_name', 'lots.short_name as short_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotTreanPending->where('lots.account_id', $account_id);
        }

        $transacPending = $lotTreanPending->get();
//        dd($transacPending);

        $tranpendAmount = $transacPending->sum('amount');
        $tranpendCount = $transacPending->count();
//        dd($tranpendCount);

        $lotOtvutQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->groupBy('transactions.lot_item_id')
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'lot_items.id as indexId', 'transactions.account_type AS TranType', 'transactions.date AS TranDate', 'transactions.id AS TranID')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotOtvutQuery->where('lots.account_id', $account_id);
        }

        $otvutItems = $lotOtvutQuery->get();
//            return $otvutItems->count();
        $return_amount = 0;
        $return_count = 0;
        foreach ($otvutItems as $key => $raw) {

            if ($raw->date <= $dateRange[1]) {
                $item = Transaction::where('lot_item_id', $raw->indexId)->orderBy('id', 'DESC')->first();

                if ($item->account_type == 'credit') {
                    $return_count++;
                    $total_pending_amount = $total_pending_amount + $raw->amount;
                    $return_amount = $return_amount + $raw->amount;
                }
            }
//                $date_data .= $otvutItems->count() . '+++++' . $raw->TranType . '---' . $raw->TranDate . '===' . $dateRange[1] . '<br />';
        }

        $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0])
                ->whereDate('transactions.date', '<=', $dateRange[1])
//                ->where('transactions.account_type', '=', 'credit')
                ->groupBy('transactions.lot_item_id')
//                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotTreanReturn->where('lots.account_id', $account_id);
        }

        $returnDateItems = $lotTreanReturn->get();
//        dd($returnDateItems);
//        dd($returnDateItems->sum('amount'));
        //Pending amount end
//        $hold = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
//            ->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
//            ->whereDate('lots.date', '<=', $dateRange[1])
//            ->where('lot_items.status', 'hold')
//            ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
//            ->get();

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'hold')
                ->whereDate('lots.date', '>=', $dateRange[0])
                ->whereDate('lots.date', '<=', $dateRange[1])
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }

        $hold = $lotReturnsQuery->get();

        $hold_amount = $hold->sum('amount');
        $hold_count = $hold->count();

//        $stop = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
//            ->whereDate('lot_items.updated_at', '>=', $dateRange[0])
//            ->whereDate('lot_items.updated_at', '<=', $dateRange[1])
//            ->where('lot_items.status', 'stop')
//            ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
//            ->get();

        $lotReturnsQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                //->leftJoin('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->where('lot_items.status', 'stop')
                ->select('lots.name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount');

        if (!empty($account_id)) {
            $lotReturnsQuery->where('lots.account_id', $account_id);
        }

        $stop = $lotReturnsQuery->get();

        $stop_amount = $stop->sum('amount');
        $stop_count = $stop->count();

        $pend_stop_count = 0;
        $pend_stop_amnt = 0;
        $ret_stop_count = 0;
        $ret_stop_amnt = 0;
        if ($stop->count() > 0) {

            foreach ($stop as $row) {
                $ret_tran = Transaction::where('lot_item_id', '=', $row->lot_item_id)->count();
                if ($ret_tran > 0) {
                    $ret_stop_count++;
                    $ret_stop_amnt = $ret_stop_amnt + $row->amount;
                } else {
                    $pend_stop_count++;
                    $pend_stop_amnt = $pend_stop_amnt + $row->amount;
                }
            }
        }
        
        
        
        
        $lotTreanReturn = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0] . ' 00:00:00')
                ->whereDate('transactions.date', '<=', $dateRange[1] . ' 23:59:59')
                ->groupBy('transactions.lot_item_id')
                ->select(DB::raw('COUNT(transactions.id) as count'), 'lots.name as lot_name', 'lots.short_name as lot_short_name', 'transactions.date AS tranDate', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount')
                ->havingRaw('count % 2 = 0');

        if (!empty($account_id)) {
            $lotTreanReturn->where('lots.account_id', $account_id);
        }

//        $returnItems = $lotTreanReturn->pluck('lot_items.index')->toArray();
//dd($lotTreanReturn->get());

        $lotTreanQuery = LotItem::join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('transactions', 'transactions.lot_item_id', '=', 'lot_items.id')
                ->whereDate('transactions.date', '>=', $dateRange[0])
                ->whereDate('transactions.date', '<=', $dateRange[1])
                ->where('transactions.account_type', '=', 'debit')
                ->groupBy('transactions.lot_item_id')
                ->select('lots.short_name as lot_name', 'lot_items.date', 'lot_items.status', 'lot_items.comment', 'lot_items.index', 'lot_items.amount', 'transactions.date AS paymDate');

        if (!empty($account_id)) {
            $lotTreanQuery->where('lots.account_id', $account_id);
        }

        $prossItems = $lotTreanQuery->get();
//        dd($lotTreanReturn->count());
        
//        dd($prossItems->count() - $lotTreanReturn->count());
        
        
        

//        $data['balanceUntilDate'] = $balanceUntilDate;
//        $data['unpaidAmount'] = $total_pending_amount;
//        $data['totalExpense'] = $totalExpense;
//        $data['holdAmount'] = $hold->sum('amount');

        $data['ret_stop_count'] = $ret_stop_count;
        $data['ret_stop_amnt'] = $ret_stop_amnt;
        $data['pend_stop_count'] = $pend_stop_count;
        $data['pend_stop_amnt'] = $pend_stop_amnt;

        $data['account'] = $account;
        $data['returnDateItems'] = $returnDateItems;

        $type = \request()->input('type');

        $data['items'] = [];
//        $data['start'] = $dateRange[0];
//        $data['end'] = $dateRange[1];
//        $data['balanceUntilDate'] = $balanceUntilDate;
        $data['balanceUntilDate'] = $upto_bal;
        $data['unpaidAmount'] = $total_pending_amount;
        $data['pending_amount'] = $processing_amount;
        $data['return_amount'] = $lotTreanReturn->sum('amount');
        $data['totalExpense'] = $totalExpense;
        $data['holdAmount'] = $hold->sum('amount');
        $data['start'] = $dateRange[0];
        $data['end'] = $dateRange[1];
        $data['return_count'] = $lotTreanReturn->count();
        $data['pending_count'] = $processing_count;
        $data['paid_amount'] = $prossItems->sum('amount') - $lotTreanReturn->sum('amount');
        $data['paid_count'] = $prossItems->count() - $lotTreanReturn->count();
        $data['hold_amount'] = $hold_amount;
        $data['hold_count'] = $hold_count;
        $data['stop_amount'] = $stop_amount;
        $data['stop_count'] = $stop_count;
        $data['tranpendAmount'] = $tranpendAmount;
        $data['tranpendCount'] = $tranpendCount;
        $data['unpaidChequeAmount'] = $unpaidChequeAmount;
        $data['cashbookAmount'] = $cashbookAmount;
//        dd($data);

        if ($type == 'pdf') {

            $pdf = PDF::loadView('report.bank.reconciliation.export', $data);
            $fileName = 'bank_reconciliation_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName,);
        }

        if ($type == 'excel') {
            $fileName = 'bank_reconciliation_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.bank.reconciliation.export', $data), $fileName);
        }

        return "Export type is invalid";
    }

    function budgetReport() {

        $financialYears = FinancialYear::getForDropdown();

        if (request()->ajax()) {

            return "hello";
            $financialYearId = \request()->input('financial_year_id', null);

            $financialYear = FinancialYear::findOrFail($financialYearId);

            //first get the budget
            $budgetItems = BudgetItem::with('head', 'items.headItem')
                    //->whereNull('parent_id')
                    ->select('*')
                    ->selectSub(function ($query) {
                        $query->selectRaw('sum(expense_transactions.amount) as expense_amount')
                        ->from('expense_transactions')
                        ->whereColumn('expense_transactions.head_id', 'budget_items.head_id')
                        ->whereColumn('expense_transactions.head_item_id', 'budget_items.head_item_id');
                        /* ->where(function ($q) {
                          $q->whereColumn('expense_transactions.head_item_id', 'budget_items.head_item_id')
                          ->orWhereNull('budget_items.head_item_id');
                          }); */
                    }, 'expense_amount')
                    // ->where('budget_id', $id)
                    ->get();

            return "hello";
            $totalTransactionAmountThoseHaveLot = Transaction::where('financial_year_id', $financialYearId)
                    ->where('lot_item_id', '!=', null)
                    ->where('lot_item_id', '!=', 0)
                    ->sum('amount');

            return view('report.budget.table', compact('budgetItems', 'financialYear', 'totalTransactionAmountThoseHaveLot'));
        }

        return view('report.budget.report', compact('financialYears'));
    }

    function budgetReportExport() {

        $type = \request()->input('type');

        $financialYearId = \request()->input('fy', null);

        $financialYear = FinancialYear::findOrFail($financialYearId);

        //first get the budget
        $budgetItems = BudgetItem::with('head', 'items.headItem')
                //->whereNull('parent_id')
                ->select('*')
                ->selectSub(function ($query) {
                    $query->selectRaw('sum(expense_transactions.amount) as expense_amount')
                    ->from('expense_transactions')
                    ->whereColumn('expense_transactions.head_id', 'budget_items.head_id')
                    ->whereColumn('expense_transactions.head_item_id', 'budget_items.head_item_id');
                    /* ->where(function ($q) {
                      $q->whereColumn('expense_transactions.head_item_id', 'budget_items.head_item_id')
                      ->orWhereNull('budget_items.head_item_id');
                      }); */
                }, 'expense_amount')
                // ->where('budget_id', $id)
                ->get();

        $data['budgetItems'] = $budgetItems;
        $data['financialYear'] = $financialYear;

        dd("dd");

        if ($type == 'pdf') {

            return view('report.budget.export', $data);
            $pdf = PDF::loadView('report.budget.export', $data, [], 'utf-8');
            $fileName = 'budget_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.pdf';

            return $pdf->stream($fileName,);
        }

        if ($type == 'excel') {
            $fileName = 'budget_report_' . now()->setTimezone('Asia/Dhaka')->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.budget.export', $data), $fileName);
        }

        return "Export type is invalid";
    }
}
