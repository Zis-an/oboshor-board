<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\TransactionItem;
use App\Models\FinancialYear;
use App\Models\Head;
use App\Models\HeadItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LedgerReportController extends ParentController
{
    function getLedgerReport(Request $request)
    {

        if ($request->ajax()) {
            //find budget for this sub head


            $headId = $request->input('head_id', null);
            $subHeadId = $request->input('sub_head_id', null);

            $head = Head::find($headId);
            $subHead = HeadItem::find($subHeadId);

            $date = explode('~', $request->input('date'));

            $startDate = $date[0];

            $endDate = $date[1];


            //$financialYearId = $request->input('financial_year_id');

            $financialYear = FinancialYear::where('start_date', '<=', $startDate)
                ->where('end_date', '>=', $endDate)
                ->first();
//            return $financialYear->id;

            if (empty($financialYear)) {
                return "<div class='alert alert-danger'>Invalid Date Range. Date Range should be within financial year </div>";
            }
            
            $budgetId = Budget::where('financial_year_id', $financialYear->id)
                    ->where('type', 'expense')
                    ->first();
            
//            return $budgetId->id;

            //DB::enableQueryLog();

//            $budgetQuery = BudgetItem::join('budgets', 'budgets.id', '=', 'budget_items.budget_id')
//                ->join('heads', 'heads.id', '=', 'budget_items.head_id')
//                ->where("budgets.financial_year_id", $financialYear->id)
//                ->where('budgets.type', 'expense')
//                ->whereNotNull('is_office_expense')
//                ->whereNull('parent_id')
//                ->select('budget_items.*');
            
           $budgetQuery = BudgetItem::select('budget_items.*')
                   ->where('budget_id', $budgetId->id);
            
            

            if (!empty($headId)) {
                $budgetQuery->where('head_id', $headId);
            }

            if (!empty($subHeadId)) {
                $budgetQuery->where('head_item_id', $subHeadId);
            }

            $budgetAmount = $budgetQuery->get()->sum('amount');
//            return $budgetAmount;

            //return DB::getQueryLog();

            //let find expenses

            $expenseQuery = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->where('transactions.status', 'final')
                ->whereDate('transactions.date', '>=', $startDate)
                ->whereDate('transactions.date', '<=', $endDate)
                ->orderBy('transactions.date', 'ASC');

            if (!empty($headId)) {
                $expenseQuery->where('transaction_items.head_id', $headId);
            }

            if (!empty($subHeadId)) {
                $expenseQuery->where('transaction_items.head_item_id', $subHeadId);
            }

            $transactions = $expenseQuery->get();

            // return response()->json($budget);

            return view('report.ledger-report.table', compact('budgetAmount', 'transactions', 'financialYear', 'head', 'subHead'));

        }

        $financialYears = FinancialYear::getForDropdown();

//        $heads = Head::where('type', 'expense')
//            ->whereNotNull('is_office_expense')
//            ->pluck('name', 'id');
        
        $heads = Head::where('type', 'expense')->pluck('name', 'id');

        return view('report.ledger-report.report', compact('financialYears', 'heads'));

    }

    function exportLedgerReport(Request $request)
    {
        $head = $request->input('head', null);
        $subHead = $request->input('sub', null);

        $date = explode('~', $request->input('date'));

        $startDate = $date[0];

        $endDate = $date[1];


        //$financialYearId = $request->input('financial_year_id');

        $financialYear = FinancialYear::where('start_date', '<=', $startDate)
            ->where('end_date', '>=', $endDate)
            ->first();

        /*$financialYearId = $request->input('fy');

        $financialYear = FinancialYear::findOrFail($financialYearId);*/

        $budgetQuery = BudgetItem::join('budgets', 'budgets.id', '=', 'budget_items.budget_id')
            ->join('heads', 'heads.id', '=', 'budget_items.head_id')
            ->where("budgets.financial_year_id", $financialYear->id)
            ->where('budgets.type', 'expense')
            ->whereNotNull('is_office_expense')
            ->whereNull('parent_id')
            ->select('budget_items.*');

        if (!empty($head)) {
            $budgetQuery->where('budget_items.head_id', $head);
        }

        if (!empty($subHead)) {
            $budgetQuery->where('head_item_id', $subHead);
        }

        $data['financialYear'] = $financialYear;

        $data['budgetAmount'] = $budgetAmount = $budgetQuery->get()->sum('amount');

        $data['head'] = Head::find($head);
        $data['subHead'] = HeadItem::find($subHead);

        //let find expenses

        $expenseQuery = TransactionItem::join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->where('transactions.status', 'final')
            ->whereDate('transactions.date', '>=', $startDate)
            ->whereDate('transactions.date', '<=', $endDate)
            ->orderBy('transactions.date', 'ASC');;

        if (!empty($head)) {
            $expenseQuery->where('transaction_items.head_id', $head);
        }

        if (!empty($subHead)) {
            $expenseQuery->where('transaction_items.head_item_id', $subHead);
        }

        $data['startDate'] = Carbon::createFromFormat('Y-m-d', $startDate)->format('d-m-Y');
        $data['endDate'] = Carbon::createFromFormat('Y-m-d', $endDate)->format('d-m-Y');

        $data['transactions'] = $expenseQuery->get();

        $type = $request->input('type');

        return $this->handleExport('report.ledger-report.export', $type, $data, 'Ledger Report', 'L');

        /*if ($type == 'pdf') {

            $pdf = PDF::loadView('report.ledger-report.export', $data);
            $fileName = 'returned_report_' . now()->format('Y-m-d H:i:s') . '.pdf';
            return $pdf->stream($fileName);
        }

        if ($type == 'excel') {
            $fileName = 'returned_report_' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('report.ledger-report.export', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";*/

    }
}
