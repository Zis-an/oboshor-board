<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>STD Account Report</h3>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    @php
        $totalOpeningBalance = 0;
        $totalDeposit = 0;
        $totalWithdraw = 0;
        $totalCharge = 0;
        $total = 0;
        $totalProfit = 0;
    @endphp

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table" id="account_book_table">
                <thead>
                <tr>
                    <th>ক্রমিক নং</th>
                    <th>হিসেব নং</th>
                    <th>ব্যাংকের নাম</th>
                    <th>ব্রাঞ্চের নাম</th>
                    <th>প্রারম্ভিক টাকার পরিমান</th>
                    <th>মোট জমার পরিমাণ</th>
                    <th>মোট খরচের পরিমাণ</th>
                    <th>মোট সুদের পরিমাণ</th>
                    <th>কর,শুল্ক,ব্যাংক চার্জ</th>
                    <th>সর্বশেষ মোট টাকার পরিমান</th>
                </tr>
                </thead>
                <tbody>

                @foreach($accounts as $index=>$account)
                    @php
                        $opening_bal = App\Models\Transaction::where('account_id', $account->id)->where('type', '=', 'opening_balance')->first();
                        $openingBalance = $account->blance > 0 ? $account->balance : $account->opening_balance;
                        $creditAmount = $account->deposit - $account->withdraw;
                        $endBalance = $account->balance + $creditAmount;
                        $totalOpeningBalance += $openingBalance;
                        $totalDeposit += $account->deposit;
                        $totalWithdraw += $account->withdraw;
                        $total += $endBalance;
                        $totalCharge += $account->charge;
                        $totalProfit += $account->profit;

                        if(isset($opening_bal->amount) && !empty($opening_bal->amount)){
                            $openingBalance = $opening_bal->amount;
                        }
                    @endphp
                    @php
                        // head id 7 (শিক্ষক-কর্মচারীদের মাসিক বেতনের ৬% হারে প্রাপ্ত চাঁদা)
                        $six_percent = App\Models\Transaction::where('account_id', $account->id)
                                        ->where('head_id', 7)
                                        ->where('head_item_id', 110)
                                        ->where('status', 'final')
                                        ->where('account_type', 'credit')
                                        ->when($dateRange, function($query) use ($dateRange) {
                                            // Split the date range into fromDate and toDate
                                            [$fromDate, $toDate] = explode('~', $dateRange);

                                            // Apply the date range filter
                                            $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                                        })
                                        ->get();

                        // head id 4 (৭৫% হিসাবে আয়)
                        $seventy_five_percent = App\Models\Transaction::where('account_id', $account->id)
                                        ->where('head_id', 4)
                                        ->where('head_item_id', 107)
                                        ->where('status', 'final')
                                        ->where('account_type', 'credit')
                                        ->when($dateRange, function($query) use ($dateRange) {
                                            [$fromDate, $toDate] = explode('~', $dateRange);
                                            $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                                        })
                                        ->get();

                        // head id 8 (থোক/Government Fund)
                        $gf = App\Models\Transaction::where('account_id', $account->id)
                                        ->where('head_id', 8)
                                        ->where('status', 'final')
                                        ->where('account_type', 'credit')
                                        ->when($dateRange, function($query) use ($dateRange) {
                                            [$fromDate, $toDate] = explode('~', $dateRange);
                                            $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                                        })
                                        ->get();

                        // (return)
                        $return = App\Models\Transaction::where('account_id', $account->id)
                                        ->where('status', 'final')
                                        ->where('account_type', 'credit')
                                        ->whereNotNull('lot_item_id')
                                        ->when($dateRange, function($query) use ($dateRange) {
                                            [$fromDate, $toDate] = explode('~', $dateRange);
                                            $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);
                                        })
                                        ->get();

                        // (others)

                    @endphp
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$account->account_no}}</td>
                        <td>{{$account->bank->short ?? ''}}</td>
                        <td>{{$account->branch->name ?? ''}}</td>
                        <td class="text-right">{{number_format($openingBalance ?? 0, 2)}}</td>
                        <td class="text-right">
                            <a href="javascript:void(0);" data-toggle="modal" data-target="#totalProfitModal_{{$account->id}}">
                                {{ number_format(($account->deposit - $account->profit - $openingBalance) ?? 0, 2) }}
                            </a>
                        </td>
                        <td class="text-right">{{number_format(($account->withdraw - $account->charge) ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format($account->profit ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format($account->charge ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format(($account->deposit - $account->withdraw), 2)}}</td>
                    </tr>

                    <!-- Modal Structure -->
                    <div class="modal fade" id="totalProfitModal_{{$account->id}}" tabindex="-1" role="dialog"
                         aria-labelledby="totalProfitModalLabel_{{$account->id}}"
                         aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-top border-bottom border-left border-right text-center w-100">
                                            <strong>৬% হারে প্রাপ্ত চাঁদা</strong>
                                        </div>
                                        <div class="border-dark border-top border-bottom border-right text-center w-100">
                                            {{ $six_percent->isNotEmpty() ? (number_format($six_percent->sum('amount'), 2)) : 0 }}
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>৭৫% হিসাবে আয়</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            {{ $seventy_five_percent->isNotEmpty() ? (number_format($seventy_five_percent->sum('amount'), 2)) : 0 }}
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>সরকার কর্তৃক প্রদত্ত অনুদান</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            {{ $gf->isNotEmpty() ? (number_format($gf->sum('amount'), 2)) : 0 }}
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>রিটার্ন</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            {{ $return->isNotEmpty() ? (number_format($return->sum('amount'), 2)) : 0 }}
                                        </div>
                                    </div>

                                    @php
                                        $totalAmount = $six_percent->sum('amount') + $seventy_five_percent->sum('amount')
                                                        + $gf->sum('amount') + $return->sum('amount');
                                    @endphp
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>সর্বমোট</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            {{ number_format($totalAmount, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="font-weight: bold; text-align: right;">
                        সর্বমোট
                    </td>
                    <td>{{number_format($totalOpeningBalance,2)}}</td>
                    <td>{{number_format($totalDeposit, 2)}}</td>
                    <td>{{number_format($totalWithdraw, 2)}}</td>
                    <td>{{number_format($totalProfit, 2)}}</td>
                    <td>{{number_format($totalCharge, 2)}}</td>
                    <td>{{number_format($total, 2)}}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>