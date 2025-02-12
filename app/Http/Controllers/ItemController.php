<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\HeadItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends ParentController
{
    function index()
    {

        if (\request()->ajax()) {

            $items = Item::with('headItem');

            return DataTables::of($items)
                ->addColumn('actions', function ($row) {
                    return "<button class='btn btn-primary btn-sm edit-head-item-btn' data-href='/items/$row->id/edit' >Edit</button>
                            <button class='btn btn-danger btn-sm delete-head-item-btn' data-href='/items/$row->id'>Delete</button>";
                })
                ->editColumn('stock_qty', function ($row) {
                    return number_format($row->stock_qty ?? 0, 2);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('item.index');
    }

    function create()
    {

        $heads = Head::where('type', 'expense')
            ->pluck("name", 'id');

        return view("item.partials.create", compact('heads'));
    }

    function store()
    {
        //form request is performed via ajax

        $validator = Validator::make(\request()->all(), [
            'name' => 'required|string',
            'head_id' => 'required',
            'head_item_id' => 'required',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $data = \request()->only([
            'name', 'description', 'head_item_id', 'head_id'
        ]);

        $user = auth()->user();

        try {

            Item::create($data);

            return $this->respondWithSuccess('Inventory Item Added');

        } catch (\Exception $exception) {

            return $this->handleException($exception, true);
        }

    }

    function edit($id)
    {
        $item = Item::findOrFail($id);

        $heads = Head::pluck('name', 'id')->toArray();

        $headItems = HeadItem::where('head_id', $item->head_id)
            ->pluck('name', 'id');

        return view('item.partials.edit-modal', compact('item', 'heads', 'headItems'));

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

            $item = Item::findOrFail($id);

            $data = \request()->only([
                'name', 'description', 'head_item_id', 'head_id'
            ]);

            $item->update($data);

            return $this->respondWithSuccess('Updated');

        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }

    }

    function destroy($id)
    {

        $item = Item::findOrFail($id);

        try {
            $item->delete();
            return $this->respondWithSuccess('Success');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }
}
