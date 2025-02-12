<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\FinancialYear;
use App\Models\Head;
use App\Models\HeadItem;
use App\Models\IncomeHead;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Mpdf\Mpdf;

class BudgetController extends ParentController
{
    function index()
    {
        if (\request()->ajax()) {
            $type = \request()->query('type');
            $budgets = Budget::with('financialYear')->where('type', $type);
            return DataTables::of($budgets)
                ->addColumn('actions', function ($row) {
                    return "
                            <a class='btn btn-primary btn-sm view-budget-btn' href='/budgets/$row->id'>Details</a>
                            <a class='btn btn-primary btn-sm edit-branch-btn' href='/budgets/$row->id/edit'>Edit</a>
                            <button class='btn btn-danger btn-sm delete-budget-btn' data-href='/budgets/$row->id'>Delete</button>


                            <button type='button' class='btn btn-info open-budget-modal' data-toggle='modal' data-target='#budgetModal' data-id='$row->id'>Assign Heads</button>";
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('budget.index');
    }

    function show($id)
    {
        $budget = Budget::findOrFail($id);

        $currentFinancialYear = FinancialYear::find($budget->financial_year_id);

        $expName = explode('-', $currentFinancialYear->name);
        $name = implode('-', [$expName[0] - 1, $expName[1] - 1]);
        $prevFinancialYear = FinancialYear::where('name', $name)
            ->first();

        $isExportable = \request()->input('export');

        $type = $budget->type;

        $prevBudget = Budget::where("financial_year_id", $budget->financial_year_id)
            ->where("type", $type)
            ->first();

        $currentHeads = [];
        $prevHeads = [];

        if ($type == 'expense') {
            $currentHeads = Head::with(['budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                    ->where("budgets.financial_year_id", $budget->financial_year_id)
                    ->where('budgets.type', $type)
                    ->select('budget_items.*');
            }, 'items.budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                    ->where("budgets.financial_year_id", $budget->financial_year_id)
                    ->where('budgets.type', $type)
                    ->select('budget_items.*');
            },
                'transactionItems' => function ($query) use ($currentFinancialYear) {
                    $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                        ->select('transaction_items.*')
                        ->where('transactions.status', 'final')
                        ->whereBetween("date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date]);
                },
                'items.transactionItems' => function ($query) use ($currentFinancialYear) {
                    $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                        ->select('transaction_items.*')
                        ->where('transactions.status', 'final')
                        ->whereBetween("transactions.date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date]);
                }
            ])->where('type', $type)
                ->get();

            if (!empty($prevFinancialYear)) {
                $prevHeads = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
                        ->where('budgets.type', $type)
                        ->select('budget_items.*');
                }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
                        ->where('budgets.type', $type)
                        ->select('budget_items.*');
                },
                    'transactionItems' => function ($query) use ($prevFinancialYear) {
                        $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                            ->where('transactions.status', 'final')
                            ->select('transaction_items.*')
                            ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
                    },
                    'items.transactionItems' => function ($query) use ($prevFinancialYear) {
                        $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                            ->where('transactions.status', 'final')
                            ->select('transaction_items.*')
                            ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
                    }
                ])->where('type', $type)
                    ->get();
            }
        }

        if ($type == 'income') {
            $currentHeads = Head::with(['budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                    ->where("budgets.financial_year_id", $budget->financial_year_id)
                    ->where('budgets.type', $type)
                    ->select('budget_items.*');
            }, 'items.budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                    ->where("budgets.financial_year_id", $budget->financial_year_id)
                    ->where('budgets.type', $type)
                    ->select('budget_items.*');
            },
                'transactions' => function ($query) use ($currentFinancialYear) {
                    $query->whereBetween("date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date])
                        ->where('transactions.status', 'final')
                        ->select('id', 'amount', 'head_id');
                },
                'items.transactions' => function ($query) use ($currentFinancialYear) {
                    $query->whereBetween("date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date])
                        ->where('transactions.status', 'final')
                        ->select('id', 'amount', 'head_item_id');
                }
            ])->where('type', $type)
                ->get();

            if (!empty($prevFinancialYear)) {
                $prevHeads = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
                        ->where('budgets.type', $type)
                        ->select('budget_items.*');
                }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
                        ->where('budgets.type', $type)
                        ->select('budget_items.*');
                },
                    'transactions' => function ($query) use ($prevFinancialYear) {
                        $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
                            ->where('transactions.status', 'final')
                            ->select('id', 'amount', 'head_id');
                    },
                    'items.transactions' => function ($query) use ($prevFinancialYear) {
                        $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
                            ->where('transactions.status', 'final')
                            ->select('id', 'amount', 'head_item_id');
                    }
                ])->where("type", $type)
                    ->get();
            }
        }

        // ✅ Apply filtering to remove heads that have no items with status == 1
        $currentHeads = $currentHeads->filter(function ($head) {
            return $head->items->contains('status', 1);
        });

        $data['currentHeads'] = $currentHeads;
        $data['budget'] = $budget;
        $data['prevHeads'] = $prevHeads;
        $data['prevFinancialYear'] = $prevFinancialYear;
        $data['currentFinancialYear'] = $currentFinancialYear;
        $data['prevBudget'] = $prevBudget;
        $data['type'] = $type;
        $data['exportFormat'] = \request()->input('type');

        if (!empty($isExportable)) {

            $type = \request()->input('type');

            if ($type == 'pdf') {

                $html = view('budget.export-budget-details', $data);

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
                    'format' => 'a4',
                    'setAutoBottomMargin' => 'stretch'
                ]);

                $mpdf->WriteHTML($html);
                $fileName = 'budge_export_' . now()->format('Y-m-d H:i:s') . '.pdf';
                return $mpdf->Output($fileName, 'I');
            }

            if ($type == 'excel') {
                $fileName = 'budge_export_' . now()->format('Y-m-d H:i:s') . '.xlsx';
                return Excel::download(new ReportExport('budget.export-budget-details', $data), $fileName);
            }

            return "<h4>this export type is not supported</h4>";
        }

        return view('budget.show', compact('currentHeads', 'budget', 'prevHeads', 'currentHeads', 'prevFinancialYear', 'currentFinancialYear', 'prevBudget', 'type'));
    }

    function create(Request $request)
    {
        $type = \request()->query('type');

        if (\request()->ajax()) {
            $fy = \request()->input('fy');
            $prevFinancialYear = null;
            $prevBudget = 0;

            if (!empty($fy)) {
                $financialYear = FinancialYear::findOrFail($fy);
                $expName = explode('-', $financialYear->name);
                $name = implode('-', [$expName[0] - 1, $expName[1] - 1]);
                $prevFinancialYear = FinancialYear::where('name', $name)->first();
                $prevBudget = Budget::where('financial_year_id', $prevFinancialYear->id)->where("type", $type)->first();
            }
            if (!empty($prevFinancialYear)) {
                if ($type == 'expense') {
                    $headQuery = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
                            ->where('budgets.type', $type)
                            ->select('budget_items.*');
                    }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
                            ->where('budgets.type', $type)
                            ->select('budget_items.*');
                    },
                        'transactionItems' => function ($query) use ($prevFinancialYear) {
                            $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                                ->select('transaction_items.*')
                                ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
                        },
                        'items.transactionItems' => function ($query) use ($prevFinancialYear) {
                            $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                                ->select('transaction_items.*')
                                ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
                        }
                    ]);
                }
                if ($type == 'income') {
                    $headQuery = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
                            ->where('budgets.type', $type)
                            ->select('budget_items.*');
                    }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
                            ->where('budgets.type', $type)
                            ->select('budget_items.*');
                    },
                        'transactions' => function ($query) use ($prevFinancialYear) {
                            $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
                                ->select('id', 'amount', 'head_id');
                        },
                        'items.transactions' => function ($query) use ($prevFinancialYear) {
                            $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
                                ->select('id', 'amount', 'head_item_id');
                        }
                    ]);
                }
            } else {
                $headQuery = Head::with('items');
            }
//            $heads = $headQuery->where('type', $type)->get();
            $heads = $headQuery->get();
            return view('budget.create-form', compact('heads', 'type', 'prevFinancialYear', 'prevBudget'));
        }
        $financialYears = FinancialYear::getForDropdown();
        return view('budget.create', compact('financialYears'));
    }

    function store()
    {
        \request()->validate([
            'year_id' => 'required',
            'amount' => 'required',
        ]);

        $user = auth()->user();

        DB::beginTransaction();

        $type = \request()->input('type');

        try {

            $budget = Budget::create([
                'amount' => \request()->amount,
                'financial_year_id' => \request()->input('year_id'),
                'date' => now(),
                'created_by' => $user->id,
                'type' => \request()->input('type'),
            ]);

            foreach (\request()->items as $childItem) {

                $headId = $childItem['head_id'];

                $parent = BudgetItem::create([
                    'budget_id' => $budget->id,
                    'head_id' => $headId,
                    'amount' => !empty($childItem['amount']) ? $childItem['amount'] : 0,
                ]);
                if (isset($childItem['child'])) {
                    foreach ($childItem['child'] as $item) {
                        BudgetItem::create([
                            'budget_id' => $budget->id,
                            'head_id' => $headId,
                            'head_item_id' => $item['head_item_id'],
                            'amount' => !empty($item['amount']) ? $item['amount'] : 0,
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

        $budgetItems = BudgetItem::with('head', 'items.headItem')
            ->whereNull('parent_id')
            ->where('budget_id', $id)
            ->get();

        $financialYears = FinancialYear::getForDropdown();

        $heads = Head::all();
        $headItems = HeadItem::all();

        return view('budget.edit', compact('budget', 'budgetItems', 'financialYears', 'heads', 'headItems'));
    }

    function update($id)
    {
        \request()->validate([
            'amount' => 'required',
        ]);
        $user = auth()->user();
        DB::beginTransaction();
        try {
            $budget = Budget::findOrFail($id);
            $budget->amount = \request()->amount;
            $budget->save();
            foreach (\request()->items as $childItem) {
                $parenItem = BudgetItem::find($childItem['id']);
                $parenItem->amount = $childItem['amount'];
                $parenItem->save();
                if (isset($childItem['child'])) {
                    foreach ($childItem['child'] as $item) {
                        $cItem = BudgetItem::find($item['id']);
                        $cItem->amount = $item['amount'];
                        $cItem->save();
                    }
                }
            }

            toastr()->success('Budget Updated');
            DB::commit();
            return redirect()->route('budgets.index', ['type' => $budget->type]);
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

    public function getHeadsWithStatus()
    {
        $heads = Head::with('items')->get(); // Fetch heads and their items along with their statuses
        return response()->json(['heads' => $heads]);
    }

    // Update the status of selected head items
    public function updateStatus(Request $request)
    {
        $headItemIds = $request->input('head_item_ids', []); // Default to empty array if null
        $headIds = $request->input('head_ids', []); // Default to empty array if null
        $uncheckedHeadItemIds = $request->input('unchecked_head_item_ids', []); // Track unchecked items

        // Ensure arrays are not empty before trying to loop through
        if (!empty($headItemIds)) {
            // Update head items' status to 1
            HeadItem::whereIn('id', $headItemIds)->update(['status' => 1]);
        }

        // Update unchecked head items' status to 0
        if (!empty($uncheckedHeadItemIds)) {
            HeadItem::whereIn('id', $uncheckedHeadItemIds)->update(['status' => 0]);
        }

        // Update head's status to 1 if all items under the head are selected
        if (!empty($headIds)) {
            foreach ($headIds as $headId) {
                $allItemsSelected = HeadItem::where('head_id', $headId)
                        ->where('status', 1)
                        ->count() === HeadItem::where('head_id', $headId)->count();

                if ($allItemsSelected) {
                    Head::where('id', $headId)->update(['status' => 1]);
                }
            }
        }

        // ✅ Reset head status to 0 if all its items are unchecked
        $affectedHeadIds = HeadItem::whereIn('id', $uncheckedHeadItemIds)->pluck('head_id')->unique();

        foreach ($affectedHeadIds as $headId) {
            $anyItemChecked = HeadItem::where('head_id', $headId)->where('status', 1)->exists();

            if (!$anyItemChecked) {
                Head::where('id', $headId)->update(['status' => 0]);
            }
        }

        return response()->json(['message' => 'Status updated successfully']);
    }
}
