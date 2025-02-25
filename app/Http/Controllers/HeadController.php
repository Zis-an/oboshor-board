<?php

namespace App\Http\Controllers;

use App\Models\Head;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class HeadController extends ParentController
{
    function index($type)
    {
        $heads = Head::select('id', 'name', 'description', 'is_office_expense')
                ->orderBy('order', 'asc')
                ->where('type', $type);
        if (\request()->ajax()) {
            $type = \request()->type;

            

            return DataTables::of($heads)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-head-btn' data-href='/heads/$row->id/edit' >Edit</button>"
//                            <button class='btn btn-danger btn-sm delete-head-btn' data-href='heads/$row->id'>Delete</button>"
                            ;
                })
                ->editColumn('name', function ($row) {
                    if ($row->is_office_expense) {
                        return $row->name . '<span class="badge badge-primary">Office Expense</span>';
                    }
                    return $row->name;
                })
                ->rawColumns(['actions', 'name'])
                ->make(true);
        }
//         return 'hi';
        return view('head.index', compact('type', 'heads')); 
    }

    function arrange_head($type)
    {
        $heads = Head::select('id', 'name', 'description', 'is_office_expense')->orderBy('order', 'asc')->where('type', $type)->get();
        // return $heads;

        return view('head.arrange_thead', compact('heads'));
    }



    public function updateOrder(Request $request)
    {

        $order = $request->input('order');

        // Loop through the order and update each record's position
        foreach ($order as $index => $id) {
            $row = Head::find($id);
            if ($row) {
                $row->order = $index;
                $row->save();
            }
        }

        return response()->json(['success' => true]);
    }


    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name',
            'description',
            'is_office_expense'
        ]);

        $data['type'] = \request()->type;

        try {

            Head::create($data);

            return $this->respondWithSuccess('Expense Head Added');
        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }
    }

    function edit($id)
    {

        $head = Head::findOrFail($id);

        return view('head.partials.edit-modal', compact('head'));
    }

    function update($id)
    {
        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = auth()->user();

        try {

            $expenseHead = Head::findOrFail($id);

            $expenseHead->name = \request()->name;
            $expenseHead->description = \request()->description;
            $expenseHead->is_office_expense = \request()->is_office_expense;
            $expenseHead->save();

            return $this->respondWithSuccess('Updated');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

    function destroy($id)
    {

        $expenseHead = Head::findOrFail($id);

        try {
            $expenseHead->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
