<?php

namespace App\Http\Controllers;

use App\Models\LotItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Lot;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class LotItemController extends ParentController
{

    function show($id)
    {

        $lotItem = LotItem::findOrFail($id);

        return view('lot.item.view', compact('lotItem'));
    }

    public function dateTranPost(Request $request)
    {
//        $file = $request->file('file');
//        $parsedData = Excel::toArray((object) [], $file);
//        dd($parsedData[0]);
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $parsedData = Excel::toArray((object)[], $file);
//            dd($request->bank_name);
//            dd($parsedData[0]);

            $datet = '';
            foreach ($parsedData[0] as $key => $val) {
                $index = $val[1];
                $lot_index = LotItem::where('index', $index)->first();
                ////                echo $index . '=====<br />';
                if (isset($lot_index) && !empty($lot_index)) {
                    $tran_data = \App\Models\Transaction::where('lot_item_id', $lot_index->id)->first();
                    //                $tran_data->date = $val[1];
                    //
                    $carbonDate = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($val[4]);
                    //
                    //                // Format the date as needed
                    $formattedDate = $carbonDate->format('Y-m-d');
                    $tran_data->date = $formattedDate;
//              $tran_data->date = '2017-07-17';
                    //                echo $val[1] . '--' . $val[5] . '--' . $val[11] . '<br />';
                    //
                    //
//                              dd($tran_data);
                    $tran_data->save();
                    echo $tran_data->date . '<br />';
                } else {
                    echo $index . '  ____no<br />';
                }
            }
            dd('kkkk');


        }
    }

    public function oldLotSentTran()
    {
        return view('lot_item_sent.index');
    }

    public function dateTranGet()
    {
        return view('lot_item_sent.get_data');
    }

    public function oldTranPost(Request $request)
    {
//        $file = $request->file('file');
//        $parsedData = Excel::toArray((object) [], $file);
//        dd($parsedData[0]);
        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $parsedData = Excel::toArray((object)[], $file);
//            dd($request->bank_name);
//            dd($parsedData[0]);
            /*
              $datet = '';
              foreach ($parsedData[0] AS $key => $val) {
              $index = $val[5];
              $lot_index = LotItem::where('index', $index)->first();
              ////                echo $index . '=====<br />';
              if(isset($lot_index) && !empty($lot_index)){
              $tran_data = \App\Models\Transaction::where('lot_item_id', $lot_index->id)->first();
              //                $tran_data->date = $val[1];
              //
              $carbonDate = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($val[1]);
              //
              //                // Format the date as needed
              $formattedDate = $carbonDate->format('Y-m-d');
              $tran_data->date = $formattedDate;
              $tran_data->date = '2017-07-17';
              //                echo $val[1] . '--' . $val[5] . '--' . $val[11] . '<br />';
              //
              //
              ////                dd($tran_data);
              $tran_data->save();
              echo $tran_data->date . '<br />';
              }else{
              echo $index . '  ____no<br />';
              }
              }
              dd('kkkk');

             */


            $pure_data = array();
            $not_upload = array();
            $not_tran = array();
            $uplod_tran = array();
            $myindex = array();
            $not_upload_now = array();
            foreach ($parsedData[0] as $key => $val) {
                $lot_index = '';
                $index = '';
                $formattedDate = '';
                $tran_amount = '';
                if ($request->bank_name == 1) {
                    $index = $val[5];
                    $carbonDate = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($val[1]);
                    $formattedDate = $carbonDate->format('Y-m-d');
                    $tran_amount = $val[11];
                } elseif ($request->bank_name == 2) {
                    $index = $val[4];
                    $formattedDate = date('Y-m-d', strtotime($val[2]));
                    $tran_amount = $val[6];
                } elseif ($request->bank_name == 3) {
                    continue;
                } else {
                    continue;
                }

                if (isset($index) && !empty($index) && isset($formattedDate) && !empty($formattedDate)) {
//                    $index = $val[5];
//                    $carbonDate = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($val[1]);
//                    $formattedDate = $carbonDate->format('Y-m-d');

                    $lot_index = LotItem::where('index', $index)->first();
//                    dd($lot_index);
                    if (isset($lot_index) && !empty($lot_index)) {
                        $tran_data = \App\Models\Transaction::where('lot_item_id', $lot_index->id)->count();
                        if ($tran_data > 0) {
                            $uplod_tran[$key][0] = $index;
                            $uplod_tran[$key][1] = $tran_amount;
                            $uplod_tran[$key][2] = $lot_index->amount;
                            $uplod_tran[$key][3] = $formattedDate;
                        } else {
                            $not_tran[$key][0] = $index;
                            $not_tran[$key][1] = $tran_amount;
                            $not_tran[$key][2] = $lot_index->amount;
                            $not_tran[$key][3] = $formattedDate;

                            $lotItem = LotItem::findOrFail($lot_index->id);

                            $lot = Lot::where('id', $lotItem->lot_id)->first();

                            DB::beginTransaction();

                            try {

                                $lotItem->status = 'sent';

                                $lotItem->save();

                                //make transaction

                                Transaction::create([
                                    'lot_item_id' => $lotItem->id,
                                    'amount' => $lotItem->amount,
                                    'account_id' => $lot->account_id,
                                    'status' => 'final',
                                    'date' => $formattedDate,
                                    'account_type' => 'debit',
                                    'method' => 'beftn',
                                    'description' => "$lot->amount BEFTN to $lotItem->bank_name,Branch: $lotItem->branch_name, Account: $lotItem->account_no, Receiver: $lotItem->receiver_name, Lot Name: $lot->name, Index No: $lotItem->index"
                                ]);

                                DB::commit();
                                $not_tran[$key][0] = $index;
                                $not_tran[$key][1] = $tran_amount;
                                $not_tran[$key][2] = $lot_index->amount;
                                $not_tran[$key][3] = $formattedDate;
                            } catch (\Exception $e) {

                                $not_upload_now[$key][0] = $index;
                                $not_upload_now[$key][1] = $tran_amount;
                                $not_upload_now[$key][2] = 0;
                                $not_upload_now[$key][3] = $formattedDate;
                            }
                        }
                    } else {
                        $not_upload[$key][0] = $index;
                        $not_upload[$key][1] = $tran_amount;
                        $not_upload[$key][2] = 0;
                        $not_upload[$key][3] = $formattedDate;
                    }
                } else {
                    continue;
                }
            }
            return view('lot_item_sent.show_data', compact('uplod_tran', 'not_tran', 'not_upload', 'not_upload_now'));
        }
    }

    public function apiForTerbb($index_no)
    {
//        dd($index_no);

        if (isset($index_no) && !empty($index_no)) {
            $index_data = LotItem::select('lot_items.*', 'lots.short_name AS lotName', 'banks.short AS bankName')
                ->join('lots', 'lots.id', '=', 'lot_items.lot_id')
                ->join('accounts', 'accounts.id', '=', 'lots.account_id')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->where('lot_items.index', $index_no)
                ->first();
        }

        dd($index_data);
//        return($index_data);
        if (isset($index_data) && !empty($index_data)) {
            return response()->json($index_data->toarray());
        } else {
            return null;
        }
        dd($index_data);
    }

    function transactionEdit($lotItemId, $transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        return view('lot.item.edit-transaction', compact('transaction', 'lotItemId'));
    }

    function transactionUpdate(Request $request, $lotItemId, $transactionId)
    {
        $request->validate([
            'date' => 'required',
        ]);

        $transaction = Transaction::findOrFail($transactionId);
        $transaction->date = $request->date;
        $transaction->save();

        //refetch url

        $reloadUrl = route('lot-search.get-lot-item', $lotItemId);

        return response()->json(['status' => 'success', 'message' => 'updated', 'reload_url' => $reloadUrl]);
    }

}
