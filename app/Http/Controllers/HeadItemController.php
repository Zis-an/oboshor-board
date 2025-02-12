<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\HeadItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class HeadItemController extends ParentController {

//    public function index(Request $request, $type) {
//        $searched_head_id = $request->head_id ?? '';
//
//        $heads = Head::orderBy('order', 'asc')->where('type', $type)->get();
//        $itemsQuery = HeadItem::with('head')->orderBy('order', 'asc');
//
//        // Apply filters
//        if ($searched_head_id) {
//            $itemsQuery->where('head_id', $searched_head_id);
//        } else {
//            $itemsQuery->whereHas('head', function ($query) use ($type) {
//                $query->where('type', $type);
//            });
//        }
//
//        // Get the filtered items
//        $items = $itemsQuery->get();
//
//        // For Ajax response (DataTables)
//        if ($request->ajax()) {
//            return DataTables::of($items)
//                            ->addColumn('actions', function ($row) {
//                                return "<button class='btn btn-primary btn-sm edit-head-item-btn' data-href='/head-items/$row->id/edit' >Edit</button>
//                                    <button class='btn btn-danger btn-sm delete-head-item-btn' data-href='/head-items/$row->id'>Delete</button>";
//                            })
//                            ->rawColumns(['actions'])
//                            ->make(true);
//        }
//
//        // Return the view with the necessary data
//        return view('head-item.index', compact('type', 'heads', 'searched_head_id'));
//    }
    
    public function index(Request $request, $type)
    {
        $searched_head_id = $request->head_id ?? '';

        $heads = Head::orderBy('order', 'asc')->where('type', $type)->get();
        $itemsQuery = HeadItem::with('head')->orderBy('order', 'asc');

        // Apply filters
        if ($searched_head_id) {
            $itemsQuery->where('head_id', $searched_head_id);
        } else {
            $itemsQuery->whereHas('head', function ($query) use ($type) {
                $query->where('type', $type);
            });
        }

        // Get the filtered items
        $items = $itemsQuery->get();

        // For Ajax response (DataTables)
        if ($request->ajax()) {
            return DataTables::of($items)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-head-item-btn' data-href='/head-items/$row->id/edit' >Edit</button>
                                    <button class='btn btn-danger btn-sm delete-head-item-btn' data-href='/head-items/$row->id'>Delete</button>";
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        // Return the view with the necessary data
        return view('head-item.index', compact('type', 'heads', 'searched_head_id'));
    }

    function arrange_head($type) {
        $heads = HeadItem::orderBy('order', 'asc')->whereHas('head', function ($query) use ($type) {
                    $query->where("type", '=', $type);
                })->get();
        // return $heads;

        return view('head-item.arrange_thead', compact('heads'));
    }

    public function updateOrder(Request $request) {

        $order = $request->input('order');

        // Loop through the order and update each record's position
        foreach ($order as $index => $id) {
            $row = HeadItem::find($id);
            if ($row) {
                $row->order = $index;
                $row->save();
            }
        }

        return response()->json(['success' => true]);
    }

    function create() {
        $type = \request()->input('type');

        $heads = Head::where('type', $type)
                ->pluck('name', 'id')
                ->toArray();

        return view("head-item.partials.create", compact('heads', 'type'));
    }

    function store() {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
                    'name' => 'required|string',
                    'head_id' => 'string',
                    'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name',
            'description',
            'head_id'
        ]);

        $user = auth()->user();

        try {

            HeadItem::create($data);

            return $this->respondWithSuccess('Expense Head Added');
        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }
    }

    function edit($id) {

        $headItem = HeadItem::findOrFail($id);

        $heads = Head::pluck('name', 'id')->toArray();

        return view('head-item.partials.edit-modal', compact('headItem', 'heads'));
    }

    function update($id) {
        $validator = Validator::make(\request()->all(), [
                    'name' => 'required|string',
                    'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {

            $expenseHeadItem = HeadItem::findOrFail($id);

            $expenseHeadItem->name = \request()->name;
            $expenseHeadItem->description = \request()->description;
            $expenseHeadItem->head_id = \request()->head_id;
            $expenseHeadItem->save();

            return $this->respondWithSuccess('Updated');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

    function destroy($id) {

        $expenseHead = HeadItem::findOrFail($id);

        try {
            $expenseHead->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

    function getHeadItems() {
        $head_id = \request()->input('head_id');

        $items = HeadItem::where('head_id', $head_id)->get();

        return response()->json($items);
    }
}
