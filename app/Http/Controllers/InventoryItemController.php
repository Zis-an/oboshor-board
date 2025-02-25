<?php

namespace App\Http\Controllers;

use App\Models\Head;
use App\Models\HeadItem;
use App\Models\Item;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function itemData()
    {
        $inventoryItems = Item::with('headItem')->get(); 
        $heads = Head::where('type', 'expense')->pluck("name", 'id');
        $headItems = HeadItem::pluck('name', 'id');
        return view('inventoryItem.index', compact('inventoryItems', 'heads', 'headItems'));
    }

    public function getHeadItems($headId)
    {
        $headItems = HeadItem::where('head_id', $headId)->pluck('name', 'id');
        return response()->json($headItems);
    }



    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'head_id' => 'required',
            'head_item_id' => 'required',
        ]);

        Item::create($request->all());

        return redirect()->route('items.data.section')
            ->with('success','Item created successfully.');
    }


    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'head_id' => 'required',
            'head_item_id' => 'required',
        ]);

        $item = Item::find($id);
        $item->update($request->all());

        return redirect()->route('items.data.section')
            ->with('success','Item updated successfully');
    }

    public function deleteItem($id)
    {
        Item::find($id)->delete();

        return redirect()->route('items.data.section')
            ->with('success','Item deleted successfully');
    }

    
}
