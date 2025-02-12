<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\BudgetItem;
use App\Models\FinancialYear;

//use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Head;
use App\Models\HeadItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Mpdf\Mpdf;

class BudgetReportController extends Controller
{

    function budgetReport()
    {
        $financialYears = FinancialYear::getForDropdown();
        $heads = Head::all();
        if (request()->ajax()) {
            $financialYearId = \request()->input('financial_year_id', null);
            $financialYear = FinancialYear::findOrFail($financialYearId);

//            $budgetItems = BudgetItem::with('head', 'headItem')
//                ->with("transactions", function ($query) use ($financialYear) {
//                    $query->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
//                        ->where('transactions.status', '=', 'final')
//                        ->whereBetween("date", [$financialYear->start_date, $financialYear->end_date]);
//                })
//                ->join('budgets', 'budgets.id', '=', 'budget_items.budget_id')
//                ->where('budgets.financial_year_id', $financialYear->id)
//                ->select('budget_items.*')
//                ->where('budgets.type', 'expense')
//                ->get();

            // Newly Added
            $selectedHeadIds = request()->input('heads', []); // Retrieve selected heads from the request

            $budgetItems = BudgetItem::with('head', 'headItem')
                ->with("transactions", function ($query) use ($financialYear) {
                    $query->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
                        ->where('transactions.status', '=', 'final')
                        ->whereBetween("date", [$financialYear->start_date, $financialYear->end_date]);
                })
                ->join('budgets', 'budgets.id', '=', 'budget_items.budget_id')
                ->where('budgets.financial_year_id', $financialYear->id)
                ->select('budget_items.*')
                ->where('budgets.type', 'expense');

            if (!empty($selectedHeadIds)) {
                $budgetItems = $budgetItems->whereIn('budget_items.head_id', $selectedHeadIds); // Filter by selected heads
            }

            $budgetItems = $budgetItems->get();
            // Newly Added

            $totalTransactionAmountThoseHaveLot = Transaction::whereDate('date', '>=', $financialYear->start_date)
                ->whereDate('date', '<=', $financialYear->end_date)
                ->where('lot_item_id', '!=', null)
                ->where('lot_item_id', '!=', 0)
                ->sum('amount');
            return view('report.budget.table', compact('budgetItems', 'financialYear', 'totalTransactionAmountThoseHaveLot'));
        }
        return view('report.budget.report', compact('financialYears', 'heads'));
    }

    function budgetReportExport()
    {
        $type = \request()->input('type');
        $financialYearId = \request()->input('fy', null);
        $financialYear = FinancialYear::findOrFail($financialYearId);

//        $budgetItems = BudgetItem::with('head', 'headItem')
//            ->withSum("headTransactions", 'amount')
//            ->withSum("transactions", 'amount')
//            ->join('budgets', 'budgets.id', '=', 'budget_items.budget_id')
//            ->where('budgets.financial_year_id', $financialYear->id)
//            ->where('budgets.type', 'expense')
//            ->get();

        // Newly Added
        $selectedHeadIds = request()->input('heads', []); // Retrieve selected heads from the request
        dd($selectedHeadIds);
        $budgetItems = BudgetItem::with('head', 'headItem')
            ->withSum("headTransactions", 'amount')
            ->withSum("transactions", 'amount')
            ->join('budgets', 'budgets.id', '=', 'budget_items.budget_id')
            ->where('budgets.financial_year_id', $financialYear->id)
            ->where('budgets.type', 'expense');

        if (!empty($selectedHeadIds)) {
            $budgetItems = $budgetItems->whereIn('budget_items.head_id', $selectedHeadIds); // Filter by selected heads
        }

        $budgetItems = $budgetItems->get();
        // Newly Added

         $totalTransactionAmountThoseHaveLot = Transaction::whereDate('date', '>=', $financialYear->start_date)
                ->whereDate('date', '<=', $financialYear->end_date)
                ->where('lot_item_id', '!=', null)
                ->where('lot_item_id', '!=', 0)
                ->sum('amount');
        $data['budgetItems'] = $budgetItems;
        $data['financialYear'] = $financialYear;
        $data['totalTransactionAmountThoseHaveLot'] = $totalTransactionAmountThoseHaveLot;
        if ($type == 'pdf') {
            $html = view('report.budget.export', $data);
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
            dd($data);
            return Excel::download(new ReportExport('report.budget.export', $data), $fileName);
        }
        return "Export type is invalid";
    }
}
