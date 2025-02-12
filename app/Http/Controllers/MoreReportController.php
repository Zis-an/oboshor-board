<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Account;
use App\Models\FinancialYear;
use App\Models\Lot;
use App\Models\LotItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Yajra\DataTables\Facades\DataTables;

class MoreReportController extends ParentController
{

    function ghatti()
    {

        $isExport = \request()->input('export');

        $type = \request()->input('type');

        if (!empty($isExport)) {

            if ($type == 'pdf') {

                $html = view('report.ghatti.export');

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
                    'format' => 'Legal',
                    'setAutoBottomMargin' => 'stretch'
                ]);


                $mpdf->WriteHTML($html);

                return $mpdf->Output('Data Report' . '.pdf', 'I');


            }

            if ($type == 'excel') {
                $fileName = 'budget_report_' . now()->format('Y-m-d H:i:s') . '.xlsx';
                return Excel::download(new ReportExport('report.ghatti.export', []), $fileName);
            }

            return "Export type is invalid";

        }

        return view("report.ghatti.report");
    }

    function incomeExpenseSummary()
    {

        $isExport = \request()->input('export');

        $type = \request()->input('type');

        if (!empty($isExport)) {

            if ($type == 'pdf') {

                $html = view('report.income-expense-summary.export');

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
                    'format' => 'Legal',
                    'setAutoBottomMargin' => 'stretch'
                ]);


                $mpdf->WriteHTML($html);

                return $mpdf->Output('Data Report' . '.pdf', 'I');


            }

            if ($type == 'excel') {
                $fileName = 'budget_report_' . now()->format('Y-m-d H:i:s') . '.xlsx';
                return Excel::download(new ReportExport('report.income-expense-summary.export', []), $fileName);
            }

            return "Export type is invalid";

        }


        return view("report.income-expense-summary.report");
    }

//    function fdrReport()
//    {
//        $exportable = \request()->input('export');
//
//        if (\request()->ajax() || !empty($exportable)) {
//            $fy = \request()->input('fy');
//            $dateRange = \request()->input('date_range');
//            //dd($dateRange); // Output: "2023-01-20~2023-02-25"
//
//            if ($dateRange) {
//                // Split the date range into fromDate and toDate
//                [$fromDate, $toDate] = explode('~', $dateRange);
//
//                dd($fromDate, $toDate); // Output: "2023-01-20" and "2023-02-25"
//            }
//
//            $financialYear = FinancialYear::findOrFail($fy);
//
//            $startDate = $financialYear->start_date;
//            $endDate = $financialYear->end_date;
//
//            $endDateWithTime = $endDate . ' 23:59:59';
//
//            $accounts = Account::select('accounts.*')
//                ->selectRaw("(SELECT SUM(amount) FROM transactions WHERE accounts.id = transactions.account_id AND (transactions.sub_type='opening_balance' OR transactions.type = 'opening_balance')) AS opening_balance")
//                ->selectRaw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id AND transactions.date <=  ?) as balance",
//                    [$startDate]
//                )
//                ->selectRaw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id AND transactions.date <=  ?) as end_balance",
//                    [$endDateWithTime]
//                )
//                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type='debit' AND  NOT transactions.type = 'transfer' AND transactions.date BETWEEN ? AND  ?) as charge",
//                    [$startDate, $endDateWithTime]
//                )
//                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND transactions.type = 'profit'  AND transactions.date BETWEEN ? AND  ?) as totalProfit",
//                    [$startDate, $endDateWithTime]
//                )
//                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND transactions.type = 'transfer' AND account_type = 'debit'  AND transactions.date BETWEEN ? AND  ?) as transfer_amount",
//                    [$startDate, $endDateWithTime]
//                )
//                ->where('type', '=', 'FDR')
//                ->get();
//
//            if (!empty($exportable)) {
//                $type = \request()->input('type');
//                $data['accounts'] = $accounts;
//                $data['startDate'] = $startDate;
//                $data['endDate'] = $endDate;
//                return $this->handleExport('report.fdr-account.export', $type, $data, 'fdreport', 'L');
//            }
//
//            return view('report.fdr-account.table', compact('accounts'));
//        }
//
//        $financialYears = FinancialYear::getForDropdown();
//        return view("report.fdr-account.report", compact('financialYears'));
//
//    }

    function fdrReport()
    {
        $exportable = \request()->input('export');

        if (\request()->ajax() || !empty($exportable)) {
            $fy = \request()->input('fy');
            $dateRange = \request()->input('date_range');

            if ($dateRange) {
                // Split the date range into fromDate and toDate
                [$fromDate, $toDate] = explode('~', $dateRange);
            }

            $financialYear = FinancialYear::findOrFail($fy);
            $startDate = $financialYear->start_date;
            $endDate = $financialYear->end_date;
            $endDateWithTime = $endDate . ' 23:59:59';

            // Create the query builder
            $query = Account::select('accounts.*')
                ->selectRaw("(SELECT SUM(amount) FROM transactions WHERE accounts.id = transactions.account_id AND (transactions.sub_type='opening_balance' OR transactions.type = 'opening_balance')) AS opening_balance")
                ->selectRaw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id AND transactions.date <=  ?) as balance",
                    [$startDate]
                )
                ->selectRaw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id AND transactions.date <=  ?) as end_balance",
                    [$endDateWithTime]
                )
                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type='debit' AND NOT transactions.type = 'transfer' AND transactions.date BETWEEN ? AND ?) as charge",
                    [$startDate, $endDateWithTime]
                )
                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND transactions.type = 'profit' AND transactions.date BETWEEN ? AND ?) as totalProfit",
                    [$startDate, $endDateWithTime]
                )
                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND transactions.type = 'transfer' AND account_type = 'debit' AND transactions.date BETWEEN ? AND ?) as transfer_amount",
                    [$startDate, $endDateWithTime]
                )
                ->where('type', '=', 'FDR');

            // Filter by date range if both dates are provided
            if (!empty($fromDate) && !empty($toDate)) {
                $query->whereHas('transactions', function($query) use ($fromDate, $toDate) {
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                });
            }

            // Execute the query
            $accounts = $query->get();

            // Export functionality
            if (!empty($exportable)) {
                $type = \request()->input('type');
                $data['accounts'] = $accounts;
                $data['startDate'] = $startDate;
                $data['endDate'] = $endDate;
                return $this->handleExport('report.fdr-account.export', $type, $data, 'fdreport', 'L');
            }

            // Return the view
            return view('report.fdr-account.table', compact('accounts'));
        }

        // For non-ajax requests, show the financial years dropdown
        $financialYears = FinancialYear::getForDropdown();
        return view("report.fdr-account.report", compact('financialYears'));
    }


//    function stdReport()
//    {
//
//        $exportable = \request()->input('export');
//
//        if (\request()->ajax() || !empty($exportable)) {
//
//            //$date = \request()->input('date');
//
//            //dateRange = explode('~', $date);
//
//            $fy = \request()->input('fy');
//
//
//            $financialYear = FinancialYear::findOrFail($fy);
//
//
//            $startDate = $financialYear->start_date;
//            $endDate = $financialYear->end_date;
//
//            $endDateWithTime = $endDate . ' 23:59:59';
//
//
//
//            $accounts = Account::select('accounts.*')
//                //->selectRaw("(SELECT amount FROM transactions where accounts.id = transactions.account_id AND (transactions.sub_type='opening_balance' OR transactions.type = 'opening_balance')) as opening_balance")
//
//                ->selectRaw("(SELECT SUM(amount) FROM transactions WHERE accounts.id = transactions.account_id AND (transactions.sub_type='opening_balance' OR transactions.type = 'opening_balance')) AS opening_balance")
//
//
//
//                ->selectRaw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id AND transactions.date < ?) as balance",
//                    [$startDate]
//                )
//                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type='credit' AND transactions.date BETWEEN ? AND  ?) as deposit",
//                    [$startDate, $endDateWithTime]
//                )
//                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type='debit' AND transactions.date BETWEEN ? AND  ?) as withdraw",
//                    [$startDate, $endDateWithTime]
//                )
//                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND type='profit' AND transactions.date BETWEEN ? AND  ?) as profit",
//                    [$startDate, $endDateWithTime]
//                )
//                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND transactions.type IN(?)  AND transactions.date BETWEEN ? AND  ?) as charge",
//                    ['service charge', $startDate, $endDateWithTime]
//                )
//                ->where('type', '=', 'STD')
//                ->get();
//
//            if (!empty($exportable)) {
//                $type = \request()->input('type');
//                $data['accounts'] = $accounts;
//                $data['startDate'] = $startDate;
//                $data['endDate'] = $endDate;
//                return $this->handleExport('report.std-account.export', $type, $data, 'std-report', 'L');
//            }
//
//            return view('report.std-account.table', compact('accounts'));
//
//        }
//
//        $financialYears = FinancialYear::getForDropdown();
//
//        return view("report.std-account.report", compact('financialYears'));
//
//    }

    function stdReport()
    {
        $exportable = \request()->input('export');

        if (\request()->ajax() || !empty($exportable)) {
            $fy = \request()->input('fy');
            $dateRange = \request()->input('date_range');

            if ($dateRange) {
                // Split the date range into fromDate and toDate
                [$fromDate, $toDate] = explode('~', $dateRange);
            }

            $financialYear = FinancialYear::findOrFail($fy);
            $startDate = $financialYear->start_date;
            $endDate = $financialYear->end_date;
            $endDateWithTime = $endDate . ' 23:59:59';

            // Set default dates if fromDate or toDate are empty
            if (empty($fromDate) || empty($toDate)) {
                $fromDate = $startDate;
                $toDate = $endDateWithTime;
            }

            // Create the query builder
            $query = Account::select('accounts.*')
                ->selectRaw("(SELECT SUM(amount) FROM transactions WHERE accounts.id = transactions.account_id AND (transactions.sub_type='opening_balance' OR transactions.type = 'opening_balance')) AS opening_balance")
                ->selectRaw("(SELECT SUM(IF(account_type='credit', amount, -1*amount)) FROM transactions where accounts.id = transactions.account_id AND transactions.date < ?) as balance",
                    [$startDate]
                )
                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type='credit' AND transactions.date BETWEEN ? AND ?) as deposit",
                    [$startDate, $endDateWithTime]
                )
                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND account_type='debit' AND transactions.date BETWEEN ? AND ?) as withdraw",
                    [$startDate, $endDateWithTime]
                )
                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND type='profit' AND transactions.date BETWEEN ? AND ?) as profit",
                    [$startDate, $endDateWithTime]
                )
                ->selectRaw("(SELECT SUM(amount) FROM transactions where accounts.id = transactions.account_id AND transactions.type IN(?) AND transactions.date BETWEEN ? AND ?) as charge",
                    ['service charge', $startDate, $endDateWithTime]
                )
                ->where('type', '=', 'STD');

            // Apply the date range filter if both dates are provided
            if (!empty($fromDate) && !empty($toDate)) {
                $query->whereHas('transactions', function($query) use ($fromDate, $toDate) {
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                });
            }

            // Execute the query
            $accounts = $query->get();

            // Export functionality
            if (!empty($exportable)) {
                $type = \request()->input('type');
                $data['accounts'] = $accounts;
                $data['startDate'] = $startDate;
                $data['endDate'] = $endDate;
                return $this->handleExport('report.std-account.export', $type, $data, 'std-report', 'L');
            }


            // Return the view
            return view('report.std-account.table', compact('accounts', 'dateRange'));
        }

        // For non-ajax requests, show the financial years dropdown
        $financialYears = FinancialYear::getForDropdown();
        return view("report.std-account.report", compact('financialYears'));
    }

    function fetchAccountTransactions(Request $request)
    {
        $request->validate([
            'account_id' => 'required|integer|exists:accounts,id',
        ]);

        $accountId = $request->input('account_id');
        $dateRange = $request->input('date_range');
        $dataType = $request->input('data_type');

        if($dataType === 'withdrawCharge') {
            $account = Account::find($accountId);
            $withdraw = Transaction::where('account_id', $accountId)
                ->where('account_type', 'debit')
                ->whereNotNull('head_item_id')
                ->when($dateRange, function($query) use ($dateRange) {
                    [$fromDate, $toDate] = explode('~', $dateRange);
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                })
                ->get();

            $teachersPayment = Transaction::where('account_id', $accountId)
                ->where('account_type', 'debit')
                ->whereNotNull('lot_item_id')
                ->when($dateRange, function($query) use ($dateRange) {
                    [$fromDate, $toDate] = explode('~', $dateRange);
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                })
                ->get();

            $totalAmount = $withdraw->sum('amount') + $teachersPayment->sum('amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => $account,
                    'withdraw' => $withdraw->sum('amount'),
                    'teachersPayment' => $teachersPayment->sum('amount'),
                    'totalAmount' => $totalAmount,
                ]
            ]);

        } elseif ($dataType === 'depositProfit') {
            $account = Account::find($accountId);
            $six_percent = Transaction::where('account_id', $account->id)
                ->where('head_id', 7)
                ->where('head_item_id', 110)
                ->where('status', 'final')
                ->where('account_type', 'credit')
                ->when($dateRange, function($query) use ($dateRange) {
                    [$fromDate, $toDate] = explode('~', $dateRange);
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                })
                ->get();

            $seventy_five_percent = Transaction::where('account_id', $account->id)
                ->where('head_id', 4)
                ->where('head_item_id', 107)
                ->where('status', 'final')
                ->where('account_type', 'credit')
                ->when($dateRange, function($query) use ($dateRange) {
                    [$fromDate, $toDate] = explode('~', $dateRange);
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                })
                ->get();

            $gf = Transaction::where('account_id', $account->id)
                ->where('head_id', 8)
                ->where('status', 'final')
                ->where('account_type', 'credit')
                ->when($dateRange, function($query) use ($dateRange) {
                    [$fromDate, $toDate] = explode('~', $dateRange);
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                })
                ->get();

            $return = Transaction::where('account_id', $account->id)
                ->where('status', 'final')
                ->where('account_type', 'credit')
                ->whereNotNull('lot_item_id')
                ->when($dateRange, function($query) use ($dateRange) {
                    [$fromDate, $toDate] = explode('~', $dateRange);
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                })
                ->get();

            $others = Transaction::where('account_id', $account->id)
                ->where('status', 'final')
                ->where('account_type', 'credit')
                ->where('type', '!=', 'deposit')
                ->when($dateRange, function($query) use ($dateRange) {
                    [$fromDate, $toDate] = explode('~', $dateRange);
                    $query->whereBetween('transactions.date', [$fromDate, $toDate]);
                })
                ->get();

            $totalAmount = $six_percent->sum('amount') + $seventy_five_percent->sum('amount')
                + $gf->sum('amount') + $return->sum('amount') + $others->sum('amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => $account,
                    'six_percent' => $six_percent->sum('amount'),
                    'seventy_five_percent' => $seventy_five_percent->sum('amount'),
                    'gf' => $gf->sum('amount'),
                    'return' => $return->sum('amount'),
                    'others' => $others->sum('amount'),
                    'totalAmount' => $totalAmount,
                ]
            ]);
        }
        return response()->json(['success' => false]);
    }

    function lotReport()
    {

        if (\request()->ajax()) {

            $date = \request()->input('date_range');

            $dateRange = explode('~', $date);

            $lots = LotItem::whereBetween('date', $dateRange)->select(
                'lot_items.date',
                DB::raw("SUM(IF(lot_items.status='sent', lot_items.amount, 0)) as sent_amount"),
                DB::raw("SUM(IF(lot_items.status='sent', 1, 0)) as sent_count"),
                DB::raw("SUM(IF(lot_items.status='hold', lot_items.amount, 0)) as hold_amount"),
                DB::raw("SUM(IF(lot_items.status='hold', 1, 0)) as hold_count"),
                DB::raw("SUM(IF(lot_items.status='stopped', lot_items.amount, 0)) as stopped_amount"),
                DB::raw("SUM(IF(lot_items.status='stopped', 1, 0)) as stopped_count"),
                DB::raw("SUM(IF(lot_items.status='processing', lot_items.amount, 0)) as processing_amount"),
                DB::raw("SUM(IF(lot_items.status='processing', 1, 0)) as processing_count"),
                DB::raw("SUM(IF(lot_items.status='returned', 1, 0)) as returned_count"),
                DB::raw("SUM(IF(lot_items.status='returned', lot_items.amount, 0)) as returned_amount"),
            );

            $lots->orderBy('lot_items.date', 'desc')
                ->groupBy('lot_items.date');

            return DataTables::of($lots)
                ->editColumn('sent_count', function ($row) {
                    return $row->sent_count . '(' . number_format($row->sent_amount, 2) . ')';
                })->editColumn('hold_count', function ($row) {
                    return $row->hold_count . '(' . number_format($row->hold_amount, 2) . ')';
                })->editColumn('processing_count', function ($row) {
                    return $row->processing_count . '(' . number_format($row->processing_amount, 2) . ')';
                })->editColumn('returned_count', function ($row) {
                    return $row->returned_count . '(' . number_format($row->returned_amount, 2) . ')';
                })
                ->editColumn('stopped_count', function ($row) {
                    return $row->stopped_count . '(' . number_format($row->stopped_amount, 2) . ')';
                })
                ->make(true);
        }

        return view('report.lot.date-wise-report');
    }
}
