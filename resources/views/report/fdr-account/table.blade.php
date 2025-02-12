<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    @php
        $totalOpeningBalance = 0;
        $totalProfit = 0;
        $totalCharge = 0;
        $total25 = 0;
        $total75= 0;
        $total = 0;
    @endphp

    <div class="card-body">
        <div class="table-responsive">
            <table class="w-100 table table-bordered" id="account_book_table">
                <thead>
                <tr>
                    <th>Sl</th>
                    <th>Account No.</th>
                    <th>Bank Name</th>
                    <th>Branch Name</th>
                    <th>Opening</th>
                    <th>Total Profit</th>
                    <th>Tax and Charge</th>
                    <th>Net Profit</th>
                    <th>Transferred Profit</th>
                    <th>Renewed Profit</th>
                    <th>End Balance</th>
                </tr>
                </thead>
                <tbody>


                @foreach($accounts as $index=>$account)
                    @php
                    $opening_bal = App\Models\Transaction::where('account_id', $account->id)->where('type', '=', 'opening_balance')->first();
                        $amountAfterTax = $account->totalProfit - $account->charge;
                        $totalOpeningBalance += $account->balance;
                        $totalProfit += $account->totalProfit;
                        $totalCharge += $account->charge;

                        $openingBalance = !empty($account->balance) ? $account->balance : $account->opening_balance;

                        $amount75 = $account->transfer_amount;

                        $amount25 = $account->end_balance > 0 ? $account->end_balance - $openingBalance : ($amount75 /3);

                        //$amount25 = $amountAfterTax * 25;
                        $total25 += $amount25;
                        $total75 += $amount75;
                        $total += $openingBalance + $amount25;
                        if(isset($opening_bal->amount) && !empty($opening_bal->amount)){
                $openingBalance = $opening_bal->amount;
                }

                    @endphp

                    <tr>
                        <td>{{$index + 1}}</td>
                        <td><a href="{{route('accounts.account-book', $account->id)}}">{{$account->account_no}}</a></td>
                        <td>{{$account->bank->short ?? ''}}</td>
                        <td>{{$account->branch->name ?? ''}}</td>
                        <td class="text-right">{{number_format($openingBalance, 2)}}</td>
                        <td class="text-right">{{number_format($account->totalProfit, 2)}}</td>
                        <td class="text-right">{{number_format($account->charge, 2)}}</td>
                        <td class="text-right">{{number_format(($amount75 + $amount25), 2)}}</td>
                        <td class="text-right">{{number_format(($amount75), 2)}}</td>
                        <td class="text-right">{{number_format(($amount25), 2)}}</td>
                        <td class="text-right">{{number_format(($account->end_balance), 2)}}</td>
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
                    <td>{{number_format($totalOpeningBalance,  2)}}</td>
                    <td>{{number_format($totalProfit, 2)}}</td>
                    <td>{{number_format($totalCharge, 2)}}</td>
                    <td>{{number_format(($total75 + $total25), 2)}}</td>
                    <td>{{number_format($total75, 2)}}</td>
                    <td>{{number_format($total25,2)}}</td>
                    <td>{{number_format($total, 2)}}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
