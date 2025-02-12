<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\UserItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class InventoryItemIssueController extends Controller
{
    public function index()
    {
        $issueItems = UserItem::with(['item', 'user']);
        if (\request()->ajax()) {
            return DataTables::of($issueItems)
                ->editColumn('date', function ($row) {
                    return $row->issued_at ? Carbon::parse($row->issued_at)->format('d-m-Y H:i A') : '';
                })
                ->editColumn('item_name', function ($row){
                    return $row->item->name ?? '';
                })
                ->editColumn('issued_for', function ($row){
                    return $row->user->name ?? '';
                })
                ->make(true);
        }

        return view("issue-item.index");

    }

    public function create(Request $request)
    {
        $users = User::getForDropdown();

        $items = Item::getForDropdown();

        return view('issue-item.create', compact('users', 'items'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        DB::beginTransaction();

        try {

            foreach ($request->items as $item) {
                UserItem::create([
                    'user_id' => $request->input('user_id'),
                    'item_id' => $item['item_id'],
                    'inventory_request_item_id' => $item['inventory_request_item_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'issued_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('issue-inventory-items.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => $e->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function addIssueItemRow()
    {

        $index = \request()->input('index') + 1;
        $items = Item::getForDropdown();

        return view('issue-item.row', compact('index', 'items'));

    }

}
