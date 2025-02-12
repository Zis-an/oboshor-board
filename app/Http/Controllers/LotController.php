<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Bank;
use App\Models\FinancialYear;
use App\Models\Head;
use App\Models\Lot;
use App\Models\LotItem;
use App\Models\Transaction;
use App\Services\FileService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Mpdf\Mpdf;

class LotController extends ParentController
{

    function index()
    {
        if (\request()->ajax()) {

            $accountId = \request()->input('account_id');

            $lots = Lot::leftJoin('lot_items', 'lot_items.lot_id', '=', 'lots.id')
                ->leftJoin('accounts', 'accounts.id', '=', 'lots.account_id')
                ->leftJoin('banks', 'banks.id', '=', 'accounts.bank_id')
                ->select('lots.id', 'lots.lot_number', 'lots.created_by', 'lots.name',
                    'lots.excel_file', 'lots.approval_file', 'lots.short_name', 'lots.date AS approve_date',
                    'banks.short as bank_Name',
                    DB::raw("COUNT(lots.id) as total"),
                    DB::raw("SUM(IF(lot_items.status='sent', lot_items.amount, 0)) as sent_amount"),
                    DB::raw("SUM(IF(lot_items.status='sent', 1, 0)) as sent_count"),
                    DB::raw("SUM(IF(lot_items.status='hold', lot_items.amount, 0)) as hold_amount"),
                    DB::raw("SUM(IF(lot_items.status='hold', 1, 0)) as hold_count"),
                    DB::raw("SUM(IF(lot_items.status='processing', lot_items.amount, 0)) as processing_amount"),
                    DB::raw("SUM(IF(lot_items.status='processing', 1, 0)) as processing_count"),
                    DB::raw("SUM(IF(lot_items.status='returned', 1, 0)) as returned_count"),
                    DB::raw("SUM(IF(lot_items.status='returned', lot_items.amount, 0)) as returned_amount"),
                    DB::raw("SUM(amount) as total_amount"),
                );


            if (!empty($accountId)) {
                $lots->where("account_id", $accountId);
            }

            $lots->orderBy('lots.date', 'desc')
                ->groupBy('lots.id');


            return DataTables::of($lots)
                ->addColumn('actions', function ($row) {
                    return view('lot.action-button', compact('row'));
                })
                ->editColumn('total_amount', function ($row) {
                    return number_format($row->total_amount, 2);
                })->editColumn('sent_count', function ($row){
                    return $row->sent_count . '('. number_format($row->sent_amount,2) .')';
                })->editColumn('hold_count', function ($row){
                    return $row->hold_count . '('. number_format($row->hold_amount,2) .')';
                })->editColumn('processing_count', function ($row){
                    return $row->processing_count . '('. number_format($row->processing_amount,2) .')';
                })->editColumn('returned_count', function ($row){
                    return $row->returned_count . '('. number_format($row->returned_amount,2) .')';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }


        $accounts = Account::getAccounts();

        return view('lot.index', compact('accounts'));
    }

    function show($id)
    {

        $lot = Lot::withSum('items', 'amount')->findOrFail($id);

        $export = \request()->input('export');

        $itemQuery = LotItem::where('lot_id', $lot->id);

        if (\request()->ajax()) {

            return DataTables::of($itemQuery)
                ->addColumn('checkbox', function ($row) {
                    $check = '<input type="checkbox" class="row-select" value="' . $row->id . '"';

                    if ($row->status !== 'processing') {
                        $check .= "disabled='disabled'";
                    }
                    $check .= '/>';

                    return $check;
                })
                ->addColumn('actions', function ($row) {
                    return view("lot.item.action-dropdown", compact('row'));
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'sent') {
                        return "<span class='badge badge-success'>Sent</span>";
                    } else if ($row->status == 'hold') {
                        return "<span class='badge badge-warning'>Hold</span>";
                    } else if ($row->status == 'returned') {
                        return "<span class='badge badge-danger'>Returned</span>";
                    }
                    return 'Processing';
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->rawColumns(['checkbox', 'actions', 'status'])
                ->make(true);
        }

        $items = $itemQuery->get();

        $data['items'] = $items;
        $data['lot'] = $lot;
        $type = \request()->input('type');

        //count
        $data['sent_count'] = $items->where('status', 'sent')->count();
        $data['hold_count'] = $items->where('status', 'hold')->count();
        $data['processing_count'] = $items->where('status', 'processing')->count();
        $data['returned_count'] = $items->where('status', 'returned')->count();
        $data['stop_count'] = $items->where('status', 'stop')->count();
        $data['total_count'] = $items->count();

        if ($export) {
            return $this->handleExport('lot.item.export', $type, $data);
        }

        return view('lot.view', compact('lot'));
    }

    function create()
    {
        $accounts = Account::getAccounts();
        return view('lot.create', compact('accounts'));
    }

    function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'file' => 'required',
            'date' => 'required',
            'account_id' => 'required',
        ]);

        $status = $request->input('is_old') ? 'sent' : 'processing';

        if ($request->hasFile('file')) {

            $file = $request->file('file');

            $parsedData = Excel::toArray((object)[], $file);
            //Remove header row
            $data = array_splice($parsedData[0], 7, -2);
//            dd($data);

            $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/'), $name);
            $excelFilePath = '/uploads/' . $name;

            //upload documents
            $approvalFilePath = '';
            if ($request->hasFile('approval_file')) {
                $file = request()->file('approval_file');
                $name = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/'), $name);
                $approvalFilePath = '/uploads/' . $name;
            }

            DB::beginTransaction();
            $line = 0;

            try {

                $lot = Lot::create([
                    'name' => $request->input('name'),
                    'lot_number' => $request->input('lot_number'),
                    'short_name' => $request->input('short_name'),
                    'file_page' => $request->input('file_page'),
                    'created_by' => auth()->id(),
                    'date' => $request->input('date'),
                    'account_id' => $request->input('account_id'),
                    'approval_file' => $approvalFilePath,
                    'excel_file' => $excelFilePath,
                ]);

                foreach ($data as $index => $datum) {
                    if (isset($datum[4]) && !empty($datum[4])) {
                        $dd = [
                            'lot_id' => $lot->id,
                            'serial_no' => $datum[0],
                            'applicant_serial_no' => $datum[1],
                            'date' => Carbon::createFromFormat('d/m/Y', $datum[2])->format('Y-m-d'),
                            'receiver_name' => $datum[3],
                            'index' => $datum[4],
                            'city' => $datum[5],
                            'amount' => $datum[6],
                            'bank_name' => $datum[7],
                            'account_no' => $datum[8],
                            'branch_name' => $datum[9],
                            'routing' => $datum[10],
                            'status' => $status,
                        ];

                        LotItem::create($dd);

                        $line = $index + 7;
                    }
                }

                DB::commit();

                return redirect()->route('lots.index');
            } catch (\Exception $exception) {

                DB::rollBack();

                return redirect()->back()->withErrors(['message' => $exception->getMessage() . 'Line: ' . $line]);
            }
        }

        return back()->withErrors(['message' => 'No file added']);
    }

    function edit($id)
    {
        $lot = Lot::findOrFail($id);

        $accounts = Account::getAccounts();

        return view('lot.edit', compact('lot', 'accounts'));
    }

    function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'date' => 'required',
            'account_id' => 'required',
        ]);

        //dd($request->all());


        $lot = Lot::findOrFail($id);

        $data = [
            'name' => $request->input('name'),
            'lot_number' => $request->input('lot_number'),
            'short_name' => $request->input('short_name'),
            'file_page' => $request->input('file_page'),
            'date' => $request->input('date'),
            'account_id' => $request->input('account_id'),
        ];

        $approvalFiles = (new FileService())->upload($request, 'approval_file');
        $excelFiles = (new FileService())->upload($request, 'file');
        //upload documents

        $existingApprovalFiles = $request->input('existing_files', []);

        $mergeApprovalFiles = array_merge($existingApprovalFiles, $approvalFiles);

        //dd($existingApprovalFiles, $approvalFiles, $mergeApprovalFiles);

        if (!empty($mergeApprovalFiles)) {
            $data['approval_file'] = implode('|', $mergeApprovalFiles);
        }

        if (!empty($excelFiles)) {
            $data['excel_file'] = $excelFiles[0];
        }

        $lot->update($data);

        toastr()->success("Updated");

        return redirect()->route("lots.index");

    }

    function destroy($id)
    {
        $lot = Lot::findOrFail($id);

        try {
            $lot->delete();
            return $this->handleException('Lot Deleted');
        } catch (\Exception $exception) {
            return $this->handleException($exception, true);
        }
    }

    function getSearch()
    {

        $accountId = \request()->query('acc');

        $account = null;
        if (!empty($accountId)) {
            $account = Account::find($accountId);
        }

        $accounts = Account::getAccounts();

        $banks = Bank::getForDropdown();

        $lotId = \request()->query('lot_id');

        $lotname = null;
        if (!empty($accountId)) {
            $lotname = Account::find($lotId);
        }

        $lots = Lot::getForDropdown();

        $setting = session()->get('setting');
        $activeFinancialYear = FinancialYear::where('id', $setting->active_financial_year_id)
            ->first();

//        $initialDateRange = implode('~', [$activeFinancialYear->start_date, $activeFinancialYear->end_date]);
        $initialDateRange = implode('~', ['2022-07-01', date("Y-m-d")]);

        $status = array(
            'all' => 'All',
            'processing' => 'Processing',
            'hold' => 'Hold',
            'returned' => 'Return',
            'sent' => 'Sent',
        );

        return view('lot.search.index', compact('accounts', 'accountId', 'banks', 'account', 'lotId', 'lotname', 'lots', 'initialDateRange', 'status'));
    }

    function postSearch(Request $request)
    {
        $index = $request->input('index', null);
        $routing = $request->input('routing', null);
        $lot = $request->input('lot', null);
        $accountNumber = $request->input('account', null);
        $lot = $request->input('lots_id', null);
        $ownBank = $request->input('account_id', null);
        $statusn = $request->input('lots_status', null);
        $dateRange = $request->input('date_range', null);

        if (empty($index) && empty($dateRange) && empty($lot) && empty($accountNumber) && empty($ownBank) && empty($statusn)) {
            return back()->withErrors(['Please Provide Some Data']);
        }


        $exploded = explode('~', $dateRange);

        $startDate = $exploded[0];
        $endDate = $exploded[1];

        $query = LotItem::select('lot_items.*', 'lots.name AS lotName', 'lots.date AS lotDate', 'banks.short AS bankName');
        $query->leftJoin('lots', 'lots.id', '=', 'lot_items.lot_id');
        $query->leftJoin('accounts', 'accounts.id', '=', 'lots.account_id');
        $query->leftJoin('banks', 'banks.id', '=', 'accounts.bank_id');

        if (!empty($index)) {
            $query->where('index', 'like', '%' . $index . '%');
        }

        if (!empty($lot)) {
            $query->where('lot_id', '=', $lot);
        }

        if (!empty($accountNumber)) {
            $query->where('account_no', '=', $accountNumber);
        }

        if (isset($ownBank) && !empty($ownBank)) {
            $query->where('lots.account_id', '=', $ownBank);
        }
        if (isset($statusn) && !empty($statusn) && ($statusn != 'all')) {
            $query->where('lot_items.status', 'like', '%' . $statusn . '%');
        }

//        if (!empty($startDate) && !empty($endDate)) {
//            $query->whereBetween('lots.date', [Carbon::parse($startDate), Carbon::parse($endDate)]);
//        }

        $results = $query->get();

        $accounts = Account::getTransferSupportedAccounts();

        $lots = Lot::getForDropdown();

        $setting = session()->get('setting');
        $activeFinancialYear = FinancialYear::where('id', $setting->active_financial_year_id)
            ->first();

        $initialDateRange = implode('~', ['2022-07-01', date("Y-m-d")]);
//        $initialDateRange = '';

        $status = array(
            'all' => 'All',
            'processing' => 'Processing',
            'hold' => 'Hold',
            'returned' => 'Return',
            'sent' => 'Sent',
        );

        // dd($beneficiary);
        return view('lot.search.result', compact('results', 'accounts', 'lots', 'initialDateRange', 'status', 'request', 'lot'));
    }

    function getPay($id)
    {

        $lot = Lot::with(['items'])->where('id', '=', $id)
            ->firstOrFail();

        $banks = Bank::pluck('name', 'id');

        $totalAmount = $lot->items->sum('amount');

        return view('lot.pay', compact('lot', 'banks', 'totalAmount'));
    }

    function pay($id)
    {
        $lot = Lot::with('items')
            ->where('id', $id)
            ->firstOrFail();

        $accountId = \request()->input('account');

        $totalAmount = $lot->items->sum('amount');

        $balance = Transaction::select(DB::raw("SUM(IF(type='credit', amount, -amount)) as balance"))
            ->where('account_id', $accountId)
            ->get()
            ->first()
            ->balance;

        $totalFailed = $lot->items->where('status', 'failed')->count();
        $totalSuccess = $lot->items->where('status', 'success')->count();

        if ($balance <= $totalAmount) {
            return $this->respondWithError('Account does not have enough balance');
        }

        if ($totalFailed <= 0 && $totalSuccess > 0) {
            return $this->respondWithError('Already Paid');
        }

        DB::beginTransaction();

        try {

            foreach ($lot->items as $item) {

                //now add transaction for each $item

                if ($item->status === 'success') {
                    continue;
                }

                Transaction::create([
                    'type' => 'debit',
                    'account_id' => $accountId,
                    'amount' => $item->amount,
                    'transaction_method' => 'bank',
                    'created_by' => auth()->id(),
                    'lot_item_id' => $item->id,
                ]);

                $item->status = 'success';
                $item->save();
            }

            DB::commit();

            return $this->respondWithSuccess('Lot Payment Success');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->handleException($exception, true);
        }
    }

    public function sendAmountIndex()
    {
        $id = \request()->input('selected');

        $lotItem = LotItem::findOrFail($id);

        $lot = Lot::where('id', $lotItem->lot_id)->first();

        DB::beginTransaction();

        try {

            if (!($lotItem->status == 'hold' || $lotItem->status == 'processing' || $lotItem->status == 'returned')) {
                return $this->respondWithError('only hold, Retiurned or processiing items can be send');
            }

            $lotItem->status = 'sent';

            $lotItem->save();

            //make transaction

            Transaction::create([
                'lot_item_id' => $lotItem->id,
                'amount' => $lotItem->amount,
                'account_id' => $lot->account_id,
                'status' => 'final',
                'date' => \request()->input('date'),
                'account_type' => 'debit',
                'method' => 'beftn',
                'description' => "$lot->amount BEFTN to $lotItem->bank_name,Branch: $lotItem->branch_name, Account: $lotItem->account_no, Receiver: $lotItem->receiver_name, Lot Name: $lot->name, Index No: $lotItem->index"
            ]);

            DB::commit();

            return $this->respondWithSuccess("Resend Successfully");
        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }
    }

    function sendBeftn($id)
    {

        $lot = Lot::findOrFail($id);

        $items = \request()->input('items');

        DB::beginTransaction();

        try {

            foreach ($items as $item) {

                $lotItem = LotItem::findOrFail($item);

                if ($lotItem->status == 'sent') {
                    return $this->respondWithError('selected items includes SENT Item');
                }

                $lotItem->status = 'sent';
                $lotItem->comment = \request()->input('comment');
                $lotItem->save();

                //make transaction

                Transaction::create([
                    'lot_item_id' => $lotItem->id,
                    'amount' => $lotItem->amount,
                    'account_id' => $lot->account_id,
                    'status' => 'final',
                    'date' => request()->input('date'),
                    'account_type' => 'debit',
                    'method' => 'beftn',
                    'description' => "$lot->amount BEFTN to $lotItem->bank_name,Branch: $lotItem->branch_name, Account: $lotItem->account_no, Receiver: $lotItem->receiver_name, Lot Name: $lot->name, Index No: $lotItem->index"
                ]);
            }

            DB::commit();

            return $this->respondWithSuccess("Successfully");
        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }
    }

    function hold(Request $request)
    {

        $id = \request()->input('selected');

        $lotItem = LotItem::findOrFail($id);

        DB::beginTransaction();

        try {


            if ($lotItem->status == 'sent') {
                return $this->respondWithError('selected items includes SENT Item');
            }

            $files = (new FileService())->upload($request, 'file');

            $lotItem->status = 'hold';
            $lotItem->comment = \request()->input('comment');
            $lotItem->hold_file = !empty($files) ? $files[0] : null;
            $lotItem->save();

            DB::commit();

            return $this->respondWithSuccess('Items Hold Successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondWithError($exception->getMessage());
        }
    }

    function resend(Request $request, $id)
    {


        $lot = Lot::findOrFail($id);

        $id = \request()->input('selected');

        $lotItem = LotItem::findOrFail($id);

        DB::beginTransaction();

        try {


            if (!($lotItem->status == 'hold' || $lotItem->status == 'returned')) {
                return $this->respondWithError('only hold or returned items can be resend');
            }

            $lotItem->status = 'sent';
            $lotItem->comment = \request()->input('comment');

            $files = (new FileService())->upload($request, 'file');
            $lotItem->resend_file = !empty($files) ? $files[0] : null;

            $lotItem->save();

            //make transaction

            Transaction::create([
                'lot_item_id' => $lotItem->id,
                'amount' => $lotItem->amount,
                'account_id' => $lot->account_id,
                'status' => 'final',
                'date' => \request()->input('date'),
                'method' => 'beftn',
                'account_type' => 'debit',
                'description' => "$lot->amount PAID to $lotItem->receiver_name (Routing No. $lotItem->routing_no)"
            ]);

            DB::commit();

            return $this->respondWithSuccess("Resend Successfully");
        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }
    }

    function confirmation($id)
    {
        $id = \request()->input('id');
        $type = \request()->input('type');

        $item = LotItem::findOrFail($id);

        return view('lot.item.confirmation', compact('item', 'type'));
    }

    public function returnIndex(Request $request)
    {

        $id = \request()->input('selected');

        $lotItem = LotItem::findOrFail($id);

        $lot = Lot::where('id', $lotItem->lot_id)->first();

        $commnt = \request()->input('comment');

        $withCredit = \request()->input('with_credit');

        DB::beginTransaction();

        try {

            if ($lotItem->status !== 'sent') {
                return $this->respondWithError('Only Sent Items Can be returned');
            }

            $files = (new FileService())->upload($request, 'file');
            $lot->return_file = !empty($files) ? $files[0] : null;

            $lotItem->status = 'returned';
            $lotItem->comment = \request()->input('comment');
            $lotItem->save();

            //make transaction

            if ($withCredit) {

                //find previous transaction
                $prevTransaction = Transaction::where('lot_item_id', $id)
                    ->first();

                Transaction::create([
                    'lot_item_id' => $lotItem->id,
                    'amount' => $lotItem->amount,
                    'account_id' => $lot->account_id,
                    'transaction_id' => $prevTransaction->id,
                    'status' => 'final',
                    'date' => \request()->input('date'),
                    'method' => 'beftn',
                    'account_type' => 'credit',
                    'type' => 'returned',
                    'description' => "Returned from $lotItem->receiver_name, Index No.: $lotItem->index, Commnt: $commnt"
                ]);
            }


            DB::commit();

            return $this->respondWithSuccess("Successfully");
        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }
    }

    public function stopIndex(Request $request)
    {

        $id = \request()->input('stopselected');

        $lotItem = LotItem::findOrFail($id);


        DB::beginTransaction();

        try {


            $files = (new FileService())->upload($request, 'file');

            if ($lotItem->status == 'sent') {
                return $this->respondWithError('Only Retured and pending Items Can be Stop');
            }


            $lotItem->status = 'stop';
            $lotItem->stop_file = !empty($files) ? $files[0] : '';
            $lotItem->comment = \request()->input('comment');
            $lotItem->save();

            DB::commit();

            return $this->respondWithSuccess("Successfully");
        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }
    }

    function return(Request $request, $id)
    {
        $lot = Lot::findOrFail($id);

        $id = \request()->input('selected');

        $lotItem = LotItem::findOrFail($id);

        $withCredit = \request()->input('with_credit');

        DB::beginTransaction();

        try {

            if ($lotItem->status !== 'sent') {
                return $this->respondWithError('Only Selected Items Can be returned');
            }

            $files = (new FileService())->upload($request, 'file');
            $lotItem->return_file = !empty($files) ? $files[0] : null;

            $lotItem->status = 'returned';
            $lotItem->comment = \request()->input('comment');
            $lotItem->save();

            //make transaction

            if ($withCredit) {

                //find previous transaction
                $prevTransaction = Transaction::where('lot_item_id', $id)
                    ->first();

                Transaction::create([
                    'lot_item_id' => $lotItem->id,
                    'amount' => $lotItem->amount,
                    'account_id' => $lot->account_id,
                    'transaction_id' => $prevTransaction->id,
                    'status' => 'final',
                    'date' => \request()->input('date'),
                    'method' => 'beftn',
                    'account_type' => 'credit',
                    'type' => 'returned',
                    'description' => "$lot->amount Returned from $lotItem->receiver_name (Routing No. $lotItem->routing, Index No. $lotItem->index)"
                ]);
            }


            DB::commit();

            return $this->respondWithSuccess("Successfully");
        } catch (\Exception $e) {
            return $this->handleException($e, true);
        }
    }

    public function getLotItem($id)
    {
        $lotItem = LotItem::where('id', $id)->first();
        $lot = Lot::where('id', $lotItem->lot_id)->first();
        $bankAccount = Account::where('id', $lot->account_id)->first();
        $transactionItems = Transaction::where('lot_item_id', $id)->orderBy('date', 'ASC')->get();

        return view('lot.item.view', compact('lotItem', 'lot', 'bankAccount', 'transactionItems'));
    }

    function addLotItem($id)
    {
        $lot = Lot::findOrFail($id);
        return view('lot.item.create', compact('lot'));
    }

    function postLotItem(Request $request, $id)
    {
        $request->validate([
            "applicant_serial_no" => 'required',
            "date" => 'required',
            "receiver_name" => 'required',
            "index" => 'required',
            "city" => 'required',
            "amount" => 'required',
            "bank_name" => 'required',
            "branch_name" => 'required',
            "account_no" => 'required',
            "routing" => 'required'
        ]);

        $lot = Lot::findOrFail($id);

        $data = $request->only([
            "applicant_serial_no",
            "date",
            "receiver_name",
            "index",
            "city",
            "amount",
            "bank_name",
            "branch_name",
            "account_no",
            "routing",
        ]);

        $data['lot_id'] = $lot->id;
        $data['status'] = 'processing';
        LotItem::create($data);

        toastr()->success('Lot item created successfully');
        return redirect()->route('lots.show', $lot->id);
    }

    function export(Request $request)
    {

        $accountId = \request()->input('account_id');

        $lots = Lot::leftJoin('lot_items', 'lot_items.lot_id', '=', 'lots.id')
            ->leftJoin('accounts', 'accounts.id', '=', 'lots.account_id')
            ->leftJoin('banks', 'banks.id', '=', 'accounts.bank_id')
            ->select('lots.id', 'lots.lot_number', 'lots.created_by', 'lots.name',
                'lots.excel_file', 'lots.approval_file', 'lots.short_name', 'lots.date AS approve_date',
                'banks.short as bank_Name',
                DB::raw("COUNT(lots.id) as total"),
                DB::raw("SUM(IF(lot_items.status='sent', 1, 0)) as sent_count"),
                DB::raw("SUM(IF(lot_items.status='hold', 1, 0)) as hold_count"),
                DB::raw("SUM(IF(lot_items.status='processing', 1, 0)) as processing_count"),
                DB::raw("SUM(IF(lot_items.status='returned', 1, 0)) as returned_count"),
                DB::raw("SUM(amount) as total_amount"),
            );

        $bank_acc = '';
        if (!empty($accountId)) {
            $lots->where("lots.account_id", $accountId);
            $bank_acc = Account::select('accounts.*', 'banks.short as bank_name')
                ->join('banks', 'banks.id', '=', 'accounts.bank_id')
                ->where('accounts.id', $accountId)
                ->first();

        }

        $lots->orderBy('lots.date', 'desc')
            ->groupBy('lots.id');

        $data['items'] = $lots->get();
        $data['bank_acc'] = $bank_acc;

        $type = $request->input('type');

        if ($type == 'pdf') {

//            $pdf = PDF::loadView('lot.export-lot', $data);
//            $fileName = 'pending-report_' . now()->format('Y-m-d H:i:s') . '.pdf';
//            return $pdf->stream($fileName);

            $html = view('lot.export-lot', $data);

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
                'orientation' => 'L',
                'format' => 'A4',
                'setAutoBottomMargin' => 'stretch'
            ]);

            $mpdf->SetAuthor('Soroar');
            $mpdf->SetCreator('Soroar');
            $mpdf->SetHTMLFooter('
                            <div style="text-align: left;"><span class="bn-font" >পাতাঃ</span> {PAGENO}/{nbpg}</div>
                            <div style="text-align: left; font-size: 6px;">This Report Auto Generate By TERBB Accouts System on ' . date('d/m/Y H:i A', strtotime(now())) . '</div>');

            $mpdf->WriteHTML($html);
            return $mpdf->Output('lot_report_' . now()->format('Y-m-d H:i:s') . '.pdf', 'I');
        }

        if ($type == 'excel') {
            $fileName = 'lot_report_' . now()->format('Y-m-d H:i:s') . '.xlsx';
            return Excel::download(new ReportExport('lot.export-lot', $data), $fileName);
        }

        return "<h4>this export type is not supported</h4>";

    }

}
