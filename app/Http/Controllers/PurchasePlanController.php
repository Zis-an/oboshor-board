<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\FinancialYear;
use App\Models\Head;
use App\Models\HeadItem;
use App\Models\Item;
use App\Models\PurchasePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchasePlanController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {

            $budgets = Budget::with('financialYear')
                ->where('type', 'purchase');

            return DataTables::of($budgets)
                ->addColumn('actions', function ($row) {
                    return "
                            <a class='btn btn-primary btn-sm view-budget-btn' href='/purchase-plans/$row->id'>View</a>
                            <a class='btn btn-primary btn-sm edit-branch-btn' href='/purchase-plans/$row->id/edit'>Edit</a>
                            <button class='btn btn-danger btn-sm delete-budget-btn' data-href='/budgets/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('purchase-plan.index');
    }

    function show($id)
    {
        $budget = Budget::findOrFail($id);

        $budgetItems = BudgetItem::with('headItem', 'items.item')
            ->whereNull('parent_id')
            ->where('budget_id', $id)
            ->get();

        //return $budgetItems;

        //return $budgetItems;

        return view('purchase-plan.show', compact('budgetItems', 'budget'));
    }

    function create()
    {
        $headItems = HeadItem::with('head', 'items')
            ->whereHas('head', function ($query) {
                $query->where("type", '=', 'expense');
            })
            ->has('items', '>', 0)
            ->get();

        $financialYears = FinancialYear::pluck('name', 'id');

        return view('purchase-plan.create', compact('headItems', 'financialYears'));
    }

    function store()
    {

        \request()->validate([
            'year' => 'required',
            'amount' => 'required',
        ]);

        $user = auth()->user();

        DB::beginTransaction();

        $type = \request()->input('type');

        try {

            $budget = Budget::create([
                'amount' => \request()->amount,
                'year' => \request()->year,
                'date' => now(),
                'created_by' => $user->id,
                'type' => \request()->input('type'),
            ]);

            foreach (\request()->items as $childItem) {

                $headItemId = $childItem['head_item_id'];

                $headItem = HeadItem::find($headItemId);

                $head = Head::find($headItem->head_id);

                $parent = BudgetItem::create([
                    'budget_id' => $budget->id,
                    'head_item_id' => $headItemId,
                    'head_id' => $head->id,
                    'amount' => !empty($childItem['amount']) ? $childItem['amount'] : 0,
                ]);

                if (isset($childItem['child'])) {
                    foreach ($childItem['child'] as $item) {

                        BudgetItem::create([
                            'budget_id' => $budget->id,
                            'head_id' => $head->id,
                            'head_item_id' => $headItemId,
                            'item_id' => $item['item_id'],
                            'amount' => !empty($item['amount']) ? $item['amount'] : 0,
                            'quantity' => !empty($item['quantity']) ? $item['quantity'] : null,
                            'unit' => !empty($item['unit']) ? $item['unit'] : null,
                            'parent_id' => $parent->id,
                        ]);
                    }
                }

            }

            DB::commit();

            return redirect()->route('budgets.index', ['type' => $type]);

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->handleException($exception);
            return back()->withErrors($exception->getMessage());

        }


    }

    function edit($id)
    {
        $budget = Budget::findOrFail($id);

        $budgetItems = BudgetItem::with('head', 'items.head')
            ->whereNull('parent_id')
            ->where('budget_id', $id)
            ->get();

        //dd($budgetHeads);

        /* ExpenseHead::with(['items' => function ($query) use ($budget) {
         return $query->join('budget_items', 'budget_items.expense_head_item_id', '=', 'expense_head_items.id')
             ->where('budget_items.budget_id', '=', $budget->id);
     }])->get();*/


        return view('budget.edit', compact('budget', 'budgetItems'));
    }

    function update($id)
    {

        \request()->validate([
            'year' => 'required',
            'amount' => 'required',
        ]);

        $user = auth()->user();

        //dd(\request()->all());

        DB::beginTransaction();

        try {

            $budget = Budget::findOrFail($id);

            $budget->amount = \request()->amount;
            $budget->year = \request()->year;

            $budget->save();

            foreach (\request()->items as $childItem) {

                $parenItem = BudgetItem::find($childItem['id']);
                $parenItem->amount = $childItem['amount'];
                $parenItem->save();

                foreach ($childItem['child'] as $item) {
                    $cItem = BudgetItem::find($item['id']);
                    $cItem->amount = $item['amount'];
                    $cItem->save();
                }
            }

            toastr()->success('Budget Updated');

            DB::commit();
            return redirect()->route('budgets.index');

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->handleException($exception);
            return back()->withErrors($exception->getMessage());
        }
    }

    function destroy($id)
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();
        return $this->respondWithSuccess('Deleted');
    }
}
