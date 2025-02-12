<?php

namespace App\Http\Controllers;

use App\Models\InventoryRequestItem;
use App\Models\Item;
use App\Models\InventoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ItemRequestController extends Controller
{

    public function index()
    {

        if (\request()->ajax()) {
            $inventoryItems = InventoryRequest::query();
            return DataTables::of($inventoryItems)
                ->addColumn('action', function ($row) {
                    return view('item.item-request.action-buttons', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('item.item-request.index');

    }


    public function create()
    {
        $items = Item::getForDropdown();

        return view('item.item-request.create', compact('items'));
    }


    public function store(Request $request)
    {

        $user = auth()->user();

        DB::beginTransaction();

        try {

            $inventoryRequest = InventoryRequest::create([
                'title' => $request->input('title'),
                'user_id' => $user->id,
                'date' => now(),
            ]);

            foreach ($request->items as $item) {
                InventoryRequestItem::create([
                    'inventory_request_id' => $inventoryRequest->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'priority' => $item['priority']
                ]);
            }

            DB::commit();

            return redirect()->route('item-requests.index');


        } catch (\Exception $exception) {
            DB::rollback();
            return back()->withErrors(['message' => $exception->getMessage()]);
        }

    }


    public function show($id)
    {
        $inventoryRequest = InventoryRequest::with(['items.item', 'items.issuedItem'])
            ->findOrFail($id)
            ->toArray();

        return view('item.item-request.show', compact('inventoryRequest'));
    }


    public function edit(InventoryRequest $itemRequest)
    {
        //
    }


    public function update(Request $request, InventoryRequest $itemRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\InventoryRequest $itemRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryRequest $itemRequest)
    {
        //
    }

    function addRequestItemRow()
    {
        $index = \request()->input('index') + 1;
        $items = Item::getForDropdown();

        return view('item.item-request.row', compact('index', 'items'));
    }

    function issueItem($id)
    {
        $inventoryRequest = InventoryRequest::with(['items.item', 'user'])
            ->findOrFail($id);

        return view('item.item-request.issue-request-item', compact('inventoryRequest'));
    }

}
