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

            $budgets = Budget::with('financialYear')
                ->where('type', $type);

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

//    function show($id)
//    {
//        $budget = Budget::findOrFail($id);
//
//        $currentFinancialYear = FinancialYear::find($budget->financial_year_id);
//
//        $expName = explode('-', $currentFinancialYear->name);
//        $name = implode('-', [$expName[0] - 1, $expName[1] - 1]);
//        $prevFinancialYear = FinancialYear::where('name', $name)
//            ->first();
//
//        $isExportable = \request()->input('export');
//
//        /* $budgetItems = BudgetItem::with('head', 'items.headItem')
//          ->whereNull('parent_id')
//          ->where('budget_id', $id)
//          ->get(); */
//
//        $type = $budget->type;
//
//        $prevBudget = Budget::where("financial_year_id", $budget->financial_year_id)
//            ->where("type", $type)
//            ->first();
//
//        $currentHeads = [];
//        $prevHeads = [];
//
//        if ($type == 'expense') {
//
//            $currentHeads = Head::with(['budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
//                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                    ->where("budgets.financial_year_id", $budget->financial_year_id)
//                    ->where('budgets.type', $type)
//                    ->select('budget_items.*');
//            }, 'items.budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
//                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                    ->where("budgets.financial_year_id", $budget->financial_year_id)
//                    ->where('budgets.type', $type)
//                    ->select('budget_items.*');
//            },
//                'transactionItems' => function ($query) use ($currentFinancialYear) {
//                    $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
//                        ->select('transaction_items.*')
//                        ->where('transactions.status', 'final')
//                        ->whereBetween("date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date]);
//                },
//                'items.transactionItems' => function ($query) use ($currentFinancialYear) {
//                    $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
//                        ->select('transaction_items.*')
//                        ->where('transactions.status', 'final')
//                        ->whereBetween("transactions.date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date]);
//                }
//            ])->where('type', $type)->orderBy('order', 'asc')
//                ->get();
//
//            //dd($currentHeads->toArray());
//
//            if (!empty($prevFinancialYear)) {
//
//                $prevHeads = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
//                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                        ->where('budgets.type', $type)
//                        ->select('budget_items.*');
//                }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
//                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                        ->where('budgets.type', $type)
//                        ->select('budget_items.*');
//                },
//                    'transactionItems' => function ($query) use ($prevFinancialYear) {
//                        $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
//                            ->where('transactions.status', 'final')
//                            ->select('transaction_items.*')
//                            ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
//                    },
//                    'items.transactionItems' => function ($query) use ($prevFinancialYear) {
//                        $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
//                            ->where('transactions.status', 'final')
//                            ->select('transaction_items.*')
//                            ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
//                    }
//                ])->where('type', $type)->orderBy('order', 'asc')
//                    ->get();
//               // dd($prevHeads->toArray());
//            }
//
//            // ✅ Apply filtering to remove heads that have no items with status == 1
//            $currentHeads = $currentHeads->filter(function ($head) {
//                return $head->items->contains('status', 1);
//            });
//        }
//
//        if ($type == 'income') {
//
//            $currentHeads = Head::with(['budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
//                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                    ->where("budgets.financial_year_id", $budget->financial_year_id)
//                    ->where('budgets.type', $type)
//                    ->select('budget_items.*');
//            }, 'items.budget' => function ($query) use ($budget, $type, $prevFinancialYear) {
//                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                    ->where("budgets.financial_year_id", $budget->financial_year_id)
//                    ->where('budgets.type', $type)
//                    ->select('budget_items.*');
//            },
//                'transactions' => function ($query) use ($currentFinancialYear) {
//                    $query->whereBetween("date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date])
//                        ->where('transactions.status', 'final')
//                        ->select('id', 'amount', 'head_id');
//                },
//                'items.transactions' => function ($query) use ($currentFinancialYear) {
//                    $query->whereBetween("date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date])
//                        ->where('transactions.status', 'final')
//                        ->select('id', 'amount', 'head_item_id');
//                }
//            ])->where('type', $type)->orderBy('order', 'asc')
//                ->get();
//
//            if (!empty($prevFinancialYear)) {
//
//                $prevHeads = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
//                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                        ->where('budgets.type', $type)
//                        ->select('budget_items.*');
//                }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
//                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                        ->where('budgets.type', $type)
//                        ->select('budget_items.*');
//                },
//                    'transactions' => function ($query) use ($prevFinancialYear) {
//                        $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
//                            ->where('transactions.status', 'final')
//                            ->select('id', 'amount', 'head_id');
//                    },
//                    'items.transactions' => function ($query) use ($prevFinancialYear) {
//                        $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
//                            ->where('transactions.status', 'final')
//                            ->select('id', 'amount', 'head_item_id');
//                    }
//                ])->where("type", $type)->orderBy('order', 'asc')
//                    ->get();
//            }
//        }
//
//        $data['currentHeads'] = $currentHeads;
//        $data['budget'] = $budget;
//        $data['prevHeads'] = $prevHeads;
//        $data['prevFinancialYear'] = $prevFinancialYear;
//        $data['currentFinancialYear'] = $currentFinancialYear;
//        $data['prevBudget'] = $prevBudget;
//        $data['type'] = $type;
//        $data['exportFormat'] = \request()->input('type');
//
//        if (!empty($isExportable)) {
//        //dd($data);
//            $type = \request()->input('type');
//
//            if ($type == 'pdf') {
//
//                $html = view('budget.export-budget-details', $data);
//
//                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
//                $fontDirs = $defaultConfig['fontDir'];
//
//                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
//                $fontData = $defaultFontConfig['fontdata'];
//
//                $mpdf = new Mpdf([
//                    'fontDir' => array_merge($fontDirs, [
//                        public_path(),
//                    ]),
//                    'fontdata' => $fontData + [
//                            'solaimanlipi' => [
//                                'R' => 'fonts/SolaimanLipi.ttf',
//                                'I' => 'fonts/SolaimanLipi.ttf',
//                                'useOTL' => 0xFF,
//                                'useKashida' => 75
//                            ]
//                        ],
//                    'default_font' => 'sans-serif',
//                    'mode' => 'utf-8',
//                    'format' => 'a4',
//                    'setAutoBottomMargin' => 'stretch'
//                ]);
//
//                $mpdf->WriteHTML($html);
//
//                // $mpdf->Output($user->name.'_'.$user->index_no.'.pdf','I');
//                $fileName = 'budge_export_' . now()->format('Y-m-d H:i:s') . '.pdf';
//                return $mpdf->Output($fileName, 'I');
//
//                // return view('budget.export-budget-details', $data);
//                // $pdf = PDF::loadView('budget.export-budget-details', $data);
//                // $fileName = 'budge_export_' . now()->format('Y-m-d H:i:s') . '.pdf';
//                // return $pdf->stream($fileName);
//            }
//
//            if ($type == 'excel') {
//                $fileName = 'budge_export_' . now()->format('Y-m-d H:i:s') . '.xlsx';
//                return Excel::download(new ReportExport('budget.export-budget-details', $data), $fileName);
//            }
//
//            return "<h4>this export type is not supported</h4>";
//        }
//
//
//        //return $budgetItems;
//
//        return view('budget.show', compact('currentHeads', 'budget', 'prevHeads', 'currentHeads', 'prevFinancialYear', 'currentFinancialYear', 'prevBudget', 'type'));
//    }

    public function show($id)
    {
        $budget = Budget::findOrFail($id);
        $currentFinancialYear = FinancialYear::find($budget->financial_year_id);

        $expName = explode('-', $currentFinancialYear->name);
        $name = implode('-', [$expName[0] - 1, $expName[1] - 1]);
        $prevFinancialYear = FinancialYear::where('name', $name)->first();

        $isExportable = request()->input('export');
        $type = $budget->type;

        $prevBudget = Budget::where("financial_year_id", $budget->financial_year_id)
            ->where("type", $type)
            ->first();

        $currentHeads = Head::with([
            'budget' => function ($query) use ($budget, $type) {
                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                    ->where("budgets.financial_year_id", $budget->financial_year_id)
                    ->where('budgets.type', $type)
                    ->select('budget_items.*');
            },
            'items.budget' => function ($query) use ($budget, $type) {
                $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                    ->where("budgets.financial_year_id", $budget->financial_year_id)
                    ->where('budgets.type', $type)
                    ->select('budget_items.*');
            },
            'transactionItems' => function ($query) use ($currentFinancialYear) {
                $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                    ->where('transactions.status', 'final')
                    ->whereBetween("date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date])
                    ->select('transaction_items.*');
            },
            'items.transactionItems' => function ($query) use ($currentFinancialYear) {
                $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                    ->where('transactions.status', 'final')
                    ->whereBetween("transactions.date", [$currentFinancialYear->start_date, $currentFinancialYear->end_date])
                    ->select('transaction_items.*');
            }
        ])
            ->where('type', $type)
            ->orderBy('order', 'asc')
            ->get();

        // Handle drag-and-drop order if request has 'order' parameter
        if (request()->has('order')) {
            $headOrder = explode(',', request()->input('order'));
            $currentHeads = $currentHeads->sortBy(function ($head) use ($headOrder) {
                return array_search($head->id, $headOrder);
            })->values();
        }

        // Fetch previous financial year's heads, keyed by ID
        $prevHeads = collect();
        if (!empty($prevFinancialYear)) {
            $prevHeads = Head::with([
                'budget' => function ($query) use ($type, $prevFinancialYear) {
                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
                        ->where('budgets.type', $type)
                        ->select('budget_items.*');
                },
                'items.budget' => function ($query) use ($type, $prevFinancialYear) {
                    $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
                        ->where("budgets.financial_year_id", $prevFinancialYear->id)
                        ->where('budgets.type', $type)
                        ->select('budget_items.*');
                },
                'transactionItems' => function ($query) use ($prevFinancialYear) {
                    $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                        ->where('transactions.status', 'final')
                        ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
                        ->select('transaction_items.*');
                },
                'items.transactionItems' => function ($query) use ($prevFinancialYear) {
                    $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
                        ->where('transactions.status', 'final')
                        ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
                        ->select('transaction_items.*');
                }
            ])
                ->where('type', $type)
                ->orderBy('order', 'asc')
                ->get()
                ->keyBy('id'); // Keyed by ID for easy lookup
        }

        // Apply filtering for expense heads (remove heads with no active items)
        if ($type == 'expense') {
            $currentHeads = $currentHeads->filter(function ($head) {
                return $head->items->contains('status', 1);
            });
        }

        // Prepare data for view
        $data = [
            'currentHeads' => $currentHeads,
            'budget' => $budget,
            'prevHeads' => $prevHeads,
            'prevFinancialYear' => $prevFinancialYear,
            'currentFinancialYear' => $currentFinancialYear,
            'prevBudget' => $prevBudget,
            'type' => $type,
            'exportFormat' => request()->input('type'),
        ];

        // Handle export functionality (PDF or Excel)
        if (!empty($isExportable)) {
            $exportType = request()->input('type');

            if ($exportType == 'pdf') {
                $html = view('budget.export-budget-details', $data);
                $mpdf = new Mpdf([
                    'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [public_path()]),
                    'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
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
                return $mpdf->Output('budget_export_' . now()->format('Y-m-d H:i:s') . '.pdf', 'I');
            }

            if ($exportType == 'excel') {
                return Excel::download(new ReportExport('budget.export-budget-details', $data), 'budget_export_' . now()->format('Y-m-d H:i:s') . '.xlsx');
            }

            return "<h4>This export type is not supported</h4>";
        }

        return view('budget.show', compact(
            'currentHeads', 'budget', 'prevHeads',
            'prevFinancialYear', 'currentFinancialYear', 'prevBudget', 'type'
        ));
    }




//    function create()
//    {
//        $type = \request()->query('type');
//        if (\request()->ajax()) {
//            $fy = \request()->input('fy');
//            $prevFinancialYear = null;
//            $prevBudget = 0;
//            if (!empty($fy)) {
//                $financialYear = FinancialYear::findOrFail($fy);
//                $expName = explode('-', $financialYear->name);
//                $name = implode('-', [$expName[0] - 1, $expName[1] - 1]);
//                $prevFinancialYear = FinancialYear::where('name', $name)
//                    ->first();
//                $prevBudget = Budget::where('financial_year_id', $prevFinancialYear->id)
//                    ->where("type", $type)
//                    ->first();
//            }
//            if (!empty($prevFinancialYear)) {
//                if ($type == 'expense') {
//
//                    $headQuery = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
//                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                            ->where('budgets.type', $type)
//                            ->select('budget_items.*');
//                    }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
//                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                            ->where('budgets.type', $type)
//                            ->select('budget_items.*');
//                    },
//                        'transactionItems' => function ($query) use ($prevFinancialYear) {
//                            $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
//                                ->select('transaction_items.*')
//                                ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
//                        },
//                        'items.transactionItems' => function ($query) use ($prevFinancialYear) {
//                            $query->join('transactions', 'transactions.id', 'transaction_items.transaction_id')
//                                ->select('transaction_items.*')
//                                ->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date]);
//                        }
//                    ]);
//                }
//                if ($type == 'income') {
//                    $headQuery = Head::with(['budget' => function ($query) use ($type, $prevFinancialYear) {
//                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                            ->where('budgets.type', $type)
//                            ->select('budget_items.*');
//                    }, 'items.budget' => function ($query) use ($type, $prevFinancialYear) {
//                        $query->join('budgets', 'budgets.id', 'budget_items.budget_id')
//                            ->where("budgets.financial_year_id", $prevFinancialYear->id)
//                            ->where('budgets.type', $type)
//                            ->select('budget_items.*');
//                    },
//                        'transactions' => function ($query) use ($prevFinancialYear) {
//                            $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
//                                ->select('id', 'amount', 'head_id');
//                        },
//                        'items.transactions' => function ($query) use ($prevFinancialYear) {
//                            $query->whereBetween("date", [$prevFinancialYear->start_date, $prevFinancialYear->end_date])
//                                ->select('id', 'amount', 'head_item_id');
//                        }
//                    ]);
//                }
//            } else {
//                $headQuery = Head::with('items');
//            }
//            $heads = $headQuery->where('type', $type)
//                ->get();
//            return view('budget.create-form', compact('heads', 'type', 'prevFinancialYear', 'prevBudget', 'type'));
//        }
//        $financialYears = FinancialYear::getForDropdown();
//        return view('budget.create', compact('financialYears'));
//    }

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

//    function store()
//    {
//        \request()->validate([
//            'year_id' => 'required',
//            'amount' => 'required',
//        ]);
//        $user = auth()->user();
//        DB::beginTransaction();
//        $type = \request()->input('type');
//        try {
//            $budget = Budget::create([
//                'amount' => \request()->amount,
//                'financial_year_id' => \request()->input('year_id'),
//                'date' => now(),
//                'created_by' => $user->id,
//                'type' => \request()->input('type'),
//            ]);
//            foreach (\request()->items as $childItem) {
//                $headId = $childItem['head_id'];
//                $parent = BudgetItem::create([
//                    'budget_id' => $budget->id,
//                    'head_id' => $headId,
//                    'amount' => !empty($childItem['amount']) ? $childItem['amount'] : 0,
//                ]);
//                if (isset($childItem['child'])) {
//                    foreach ($childItem['child'] as $item) {
//                        BudgetItem::create([
//                            'budget_id' => $budget->id,
//                            'head_id' => $headId,
//                            'head_item_id' => $item['head_item_id'],
//                            'amount' => !empty($item['amount']) ? $item['amount'] : 0,
//                            'parent_id' => $parent->id,
//                        ]);
//                    }
//                }
//            }
//            DB::commit();
//            return redirect()->route('budgets.index', ['type' => $type]);
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            $this->handleException($exception);
//            return back()->withErrors($exception->getMessage());
//        }
//    }

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

//    function edit($id)
//    {
//        $budget = Budget::findOrFail($id);
//        $budgetItems = BudgetItem::with('head', 'items.headItem')
//            ->whereNull('parent_id')
//            ->where('budget_id', $id)
//            ->get();
//        $financialYears = FinancialYear::getForDropdown();
//        //dd($budgetHeads);
//        /* ExpenseHead::with(['items' => function ($query) use ($budget) {
//          return $query->join('budget_items', 'budget_items.expense_head_item_id', '=', 'expense_head_items.id')
//          ->where('budget_items.budget_id', '=', $budget->id);
//          }])->get(); */
//        return view('budget.edit', compact('budget', 'budgetItems', 'financialYears'));
//    }

    public function edit($id)
    {
        $budget = Budget::findOrFail($id);

        // Fetch budget items, ensuring head_id and head_item_id are included
        $budgetItems = BudgetItem::where('budget_id', $id)->get();

        $heads = Head::with('items')->get();


        $headItems = HeadItem::all();


        $financialYears = FinancialYear::getForDropdown();

        return view('budget.edit', compact('budget', 'budgetItems', 'financialYears', 'heads', 'headItems'));
    }

//    function update($id)
//    {
//        \request()->validate([
//            'amount' => 'required',
//        ]);
//        $user = auth()->user();
//        DB::beginTransaction();
//
//        try {
//            $budget = Budget::findOrFail($id);
//            $budget->amount = \request()->amount;
//            //$budget->financial_year_id = \request()->input('financial_year_id');
//            $budget->save();
//            foreach (\request()->items as $childItem) {
//
//                $parenItem = BudgetItem::find($childItem['id']);
//                $parenItem->amount = $childItem['amount'];
//                $parenItem->save();
//                if (isset($childItem['child'])) {
//                    foreach ($childItem['child'] as $item) {
//                        $cItem = BudgetItem::find($item['id']);
//                        $cItem->amount = $item['amount'];
//                        $cItem->save();
//                    }
//                }
//            }
//            toastr()->success('Budget Updated');
//            DB::commit();
//            return redirect()->route('budgets.index', ['type' => $budget->type]);
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            $this->handleException($exception);
//            return back()->withErrors($exception->getMessage());
//        }
//    }

    function update($id)
    {
        $selectedItems = json_decode(request()->input('selected_items'), true);

        DB::beginTransaction();

        try {
            $budget = Budget::findOrFail($id);

            // If selected items are present, delete existing budget items first
            if ($selectedItems != null) {
                // Delete all existing budget items for this budget
                BudgetItem::where('budget_id', $budget->id)->delete();
            }

            if ($selectedItems == null) {
                // Get the budget items array from the request
                $budgetItems = request()->input('budget_items');

                // Initialize budget amount difference
                $budgetAmountDifference = 0;

                // Loop through the budget items and update each one
                foreach ($budgetItems as $item) {
                    $budgetItem = BudgetItem::find($item['id']);

                    if ($budgetItem && $budgetItem->amount != $item['amount']) {
                        // Calculate difference
                        $difference = $item['amount'] - $budgetItem->amount;

                        // Update and save the new amount
                        $budgetItem->amount = $item['amount'];
                        $budgetItem->save();

                        // Add difference to total budget amount difference
                        $budgetAmountDifference += $difference;
                    }
                }

                // Update the budget amount (adding the total difference)
                $budget->amount += $budgetAmountDifference;
                $budget->save();

                DB::commit();

                // Return a success message and redirect
                toastr()->success('Budget Updated');
                return redirect()->route('budgets.index', ['type' => $budget->type]);
            } else {
                $totalAmount = 0;

                foreach ($selectedItems as $head) {
                    // Create new parent budget item for the head
                    $budgetItem = BudgetItem::create([
                        'budget_id' => $id,
                        'head_id' => $head['HeadId'],
                        'parent_id' => null,
                        'amount' => 0.00,
                        'actual_amount' => 0.00
                    ]);

                    foreach ($head['Items'] as $childItem) {
                        BudgetItem::create([
                            'budget_id' => $id,
                            'head_id' => $head['HeadId'],
                            'parent_id' => $budgetItem->id,
                            'head_item_id' => $childItem['ItemId'],
                            'amount' => $childItem['Amount'],
                            'actual_amount' => 0.00
                        ]);

                        $totalAmount += $childItem['Amount'];
                    }
                }

                $budget->amount = $totalAmount;
                $budget->save();
            }

            DB::commit();
            toastr()->success('Budget Updated');
            return redirect()->route('budgets.index', ['type' => $budget->type]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong! ' . $e->getMessage()]);
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

    function destroyBudgetItems(Request $request)
    {
        DB::beginTransaction();
        try {
            $budgetItem = BudgetItem::find($request->id);

            if (!$budgetItem) {
                return response()->json(['success' => false, 'message' => 'Item not found.']);
            }

            $budget = Budget::findOrFail($budgetItem->budget_id);

            if ($budgetItem->parent_id !== null) {
                // It's a child item, adjust budget amount
                $budget->amount -= $budgetItem->amount;
            }

            // Delete the item
            $budgetItem->delete();

            // Check if parent (head) has no more items, delete it too
            if ($budgetItem->parent_id === null) {
                $childItemsCount = BudgetItem::where('parent_id', $budgetItem->id)->count();
                if ($childItemsCount === 0) {
                    $budgetItem->delete();
                }
            }

            // Save the updated budget amount
            $budget->save();

            DB::commit();

            return response()->json(['success' => true, 'new_budget_amount' => $budget->amount]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
