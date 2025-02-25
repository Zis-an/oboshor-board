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

                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$account->account_no}}</td>
                        <td>{{$account->bank->short ?? ''}}</td>
                        <td>{{$account->branch->name ?? ''}}</td>
                        <td class="text-right">{{number_format($openingBalance ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format(($account->deposit - $account->profit - $openingBalance) ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format(($account->withdraw - $account->charge) ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format($account->profit ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format($account->charge ?? 0, 2)}}</td>
                        <td class="text-right">{{number_format(($account->deposit - $account->withdraw), 2)}}</td>
                    </tr>
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
