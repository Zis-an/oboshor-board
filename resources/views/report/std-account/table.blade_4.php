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


{{--                    @php                    --}}
{{--                    $six_percent = App\Models\Transaction::where('account_id', $account->id)--}}
{{--                                    ->where('head_id', 7)--}}
{{--                                    ->where('head_item_id', 110)--}}
{{--                                    ->where('status', 'final')--}}
{{--                                    ->where('account_type', 'credit')--}}
{{--                                    ->when($dateRange, function($query) use ($dateRange) {--}}
{{--                                        [$fromDate, $toDate] = explode('~', $dateRange);--}}
{{--                                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);--}}
{{--                                    })--}}
{{--                                    ->get();--}}
{{--                    $seventy_five_percent = App\Models\Transaction::where('account_id', $account->id)--}}
{{--                                    ->where('head_id', 4)--}}
{{--                                    ->where('head_item_id', 107)--}}
{{--                                    ->where('status', 'final')--}}
{{--                                    ->where('account_type', 'credit')--}}
{{--                                    ->when($dateRange, function($query) use ($dateRange) {--}}
{{--                                        [$fromDate, $toDate] = explode('~', $dateRange);--}}
{{--                                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);--}}
{{--                                    })--}}
{{--                                    ->get();--}}
{{--                    $gf = App\Models\Transaction::where('account_id', $account->id)--}}
{{--                                    ->where('head_id', 8)--}}
{{--                                    ->where('status', 'final')--}}
{{--                                    ->where('account_type', 'credit')--}}
{{--                                    ->when($dateRange, function($query) use ($dateRange) {--}}
{{--                                        [$fromDate, $toDate] = explode('~', $dateRange);--}}
{{--                                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);--}}
{{--                                    })--}}
{{--                                    ->get();--}}
{{--                    $return = App\Models\Transaction::where('account_id', $account->id)--}}
{{--                                    ->where('status', 'final')--}}
{{--                                    ->where('account_type', 'credit')--}}
{{--                                    ->whereNotNull('lot_item_id')--}}
{{--                                    ->when($dateRange, function($query) use ($dateRange) {--}}
{{--                                        [$fromDate, $toDate] = explode('~', $dateRange);--}}
{{--                                        $query->whereBetween('transactions.date', [$fromDate, $toDate . ' 23:59:59']);--}}
{{--                                    })--}}
{{--                                    ->get();--}}
{{--                    @endphp--}}


                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$account->account_no}}</td>
                        <td>{{$account->bank->short ?? ''}}</td>
                        <td>{{$account->branch->name ?? ''}}</td>
                        <td class="text-right">{{number_format($openingBalance ?? 0, 2)}}</td>
{{--                        <td class="text-right">--}}
{{--                            <a href="javascript:void(0);" data-toggle="modal" data-target="#totalProfitModal_{{$account->id}}">--}}
{{--                                {{ number_format(($account->deposit - $account->profit - $openingBalance) ?? 0, 2) }}--}}
{{--                            </a>--}}
{{--                        </td>--}}
{{--                        <td class="text-right">{{number_format(($account->withdraw - $account->charge) ?? 0, 2)}}</td>--}}



                        <td class="text-right">
                            <a
                                    href="javascript:void(0);"
                                    class="account-details-link"
                                    data-account-id="{{ $account->id }}"
                                    data-toggle="modal"
                                    data-target="#totalProfitModal_{{$account->id}}"
                                    data-type="depositProfit"
                            >
                                {{ number_format(($account->deposit - $account->profit - $openingBalance) ?? 0, 2) }}
                            </a>
                        </td>
                        <td class="text-right">
                            <a
                                    href="javascript:void(0);"
                                    class="account-details-link"
                                    data-account-id="{{ $account->id }}"
                                    data-toggle="modal"
                                    data-target="#withdrawChargeModal_{{$account->id}}"
                                    data-type="withdrawCharge"
                            >
                                {{ number_format(($account->withdraw - $account->charge) ?? 0, 2) }}
                            </a>
                        </td>


                        <td class="text-right">{{number_format($account->profit ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format($account->charge ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format(($account->deposit - $account->withdraw), 2)}}</td>
                    </tr>

                    <!-- Modal Structure -->
{{--                    <div class="modal fade" id="totalProfitModal_{{$account->id}}" tabindex="-1" role="dialog"--}}
{{--                         aria-labelledby="totalProfitModalLabel_{{$account->id}}"--}}
{{--                         aria-hidden="true">--}}
{{--                        <div class="modal-dialog" role="document">--}}
{{--                            <div class="modal-content">--}}
{{--                                <div class="modal-header">--}}
{{--                                    <h5 class="modal-title">Details</h5>--}}
{{--                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                                        <span aria-hidden="true">&times;</span>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                                --}}
{{--                                <div class="modal-body">--}}
{{--                                    <div class="d-flex justify-content-center">--}}
{{--                                        <div class="border-dark border-top border-bottom border-left border-right text-center w-100">--}}
{{--                                            <strong>৬% হারে প্রাপ্ত চাঁদা</strong>--}}
{{--                                        </div>--}}
{{--                                        <div class="border-dark border-top border-bottom border-right text-center w-100">--}}
{{--                                            {{ $six_percent->isNotEmpty() ? (number_format($six_percent->sum('amount'), 2)) : 0 }}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="d-flex justify-content-center">--}}
{{--                                        <div class="border-dark border-bottom border-left border-right text-center w-100">--}}
{{--                                            <strong>৭৫% হিসাবে আয়</strong>--}}
{{--                                        </div>--}}
{{--                                        <div class="border-dark border-bottom border-right text-center w-100">--}}
{{--                                            {{ $seventy_five_percent->isNotEmpty() ? (number_format($seventy_five_percent->sum('amount'), 2)) : 0 }}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="d-flex justify-content-center">--}}
{{--                                        <div class="border-dark border-bottom border-left border-right text-center w-100">--}}
{{--                                            <strong>সরকার কর্তৃক প্রদত্ত অনুদান</strong>--}}
{{--                                        </div>--}}
{{--                                        <div class="border-dark border-bottom border-right text-center w-100">--}}
{{--                                            {{ $gf->isNotEmpty() ? (number_format($gf->sum('amount'), 2)) : 0 }}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="d-flex justify-content-center">--}}
{{--                                        <div class="border-dark border-bottom border-left border-right text-center w-100">--}}
{{--                                            <strong>রিটার্ন</strong>--}}
{{--                                        </div>--}}
{{--                                        <div class="border-dark border-bottom border-right text-center w-100">--}}
{{--                                            {{ $return->isNotEmpty() ? (number_format($return->sum('amount'), 2)) : 0 }}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    --}}
{{--                                    @php--}}
{{--                                        $totalAmount = $six_percent->sum('amount') + $seventy_five_percent->sum('amount')--}}
{{--                                                        + $gf->sum('amount') + $return->sum('amount');--}}
{{--                                    @endphp--}}
{{--                                    <div class="d-flex justify-content-center">--}}
{{--                                        <div class="border-dark border-bottom border-left border-right text-center w-100">--}}
{{--                                            <strong>সর্বমোট</strong>--}}
{{--                                        </div>--}}
{{--                                        <div class="border-dark border-bottom border-right text-center w-100">--}}
{{--                                            {{ number_format($totalAmount, 2) }}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="modal-footer">--}}
{{--                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}



                    <!-- Modal Structure (মোট জমার পরিমাণ) -->
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
                                            <span class="six-percent">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>৭৫% হিসাবে আয়</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            <span class="seventy-five-percent">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>সরকার কর্তৃক প্রদত্ত অনুদান</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            <span class="gf">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>রিটার্ন</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            <span class="return">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>অন্যান্য</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            <span class="others">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>সর্বমোট</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            <span class="total-amount">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Structure (মোট খরচের পরিমাণ) -->
                    <div class="modal fade" id="withdrawChargeModal_{{$account->id}}" tabindex="-1" role="dialog"
                         aria-labelledby="withdrawChargeModalLabel_{{$account->id}}"
                         aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Withdrawal and Charge Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Here you can add the content that will show specific details for withdrawal and charge -->
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-top border-bottom border-left border-right text-center w-100">
                                            <strong>Withdraw Amount</strong>
                                        </div>
                                        <div class="border-dark border-top border-bottom border-right text-center w-100">
                                            <span class="withdraw-amount">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>Teachers Payment</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            <span class="charge-amount">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="border-dark border-bottom border-left border-right text-center w-100">
                                            <strong>Total</strong>
                                        </div>
                                        <div class="border-dark border-bottom border-right text-center w-100">
                                            <span class="total-amount">0</span>
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

<script>
    $(document).on('click', '.account-details-link', function () {
        let accountId = $(this).data('account-id');
        let modalTarget = $(this).data('target'); // Get the target modal ID dynamically
        let dataType = $(this).data('type'); // Get the type of data to fetch (withdrawCharge or depositProfit)

        $.ajax({
            url: "{{ route('account.transactions.fetch') }}",
            type: "POST",
            data: {
                account_id: accountId,
                _token: "{{ csrf_token() }}",
                data_type: dataType
            },
            success: function (response) {
                if (response.success) {
                    const data = response.data;
                    console.log(data);

                    if (dataType === 'withdrawCharge') {
                        $(modalTarget).find(".withdraw-amount").text(data.withdraw ? number_format(data.withdraw, 2) : 0);
                        $(modalTarget).find(".charge-amount").text(data.teachersPayment ? number_format(data.teachersPayment, 2) : 0);
                        $(modalTarget).find(".total-amount").text(number_format(data.totalAmount, 2));
                    } else if (dataType === 'depositProfit') {
                        // Populate the modal for Deposit and Profit data
                        $(modalTarget).find(".six-percent").text(data.six_percent ? number_format(data.six_percent, 2) : 0);
                        $(modalTarget).find(".seventy-five-percent").text(data.seventy_five_percent ? number_format(data.seventy_five_percent, 2) : 0);
                        $(modalTarget).find(".gf").text(data.gf ? number_format(data.gf, 2) : 0);
                        $(modalTarget).find(".return").text(data.return ? number_format(data.return, 2) : 0);
                        $(modalTarget).find(".others").text(data.others ? number_format(data.others, 2) : 0);
                        $(modalTarget).find(".total-amount").text(number_format(data.totalAmount, 2));
                    }
                } else {
                    alert("Unable to fetch account details.");
                }
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
            }
        });
    });


    function number_format(number, decimals = 2) {
        return number.toFixed(decimals);
    }
</script>
