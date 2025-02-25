<html>
<head>
    <style>
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .table-bordered, td, th {
            border: 1px solid #494d54;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

    </style>
</head>
</html>


<div>

    <div>
        @php
            $closingBalance = $balanceUntilDate;
            $netBalance = 0;
        @endphp
        <div class="card-body">
            <table class="table table-bordered" id="lot_hold_table">
                <thead>
                <tr style="padding: 5px">
                    <td colspan="{{$account->id == 49 ? 2 : 3}}" style="text-align: center;">
                        <span style="text-align: center; font-weight: bold!important; font-size: 14px!important;">Non Government Teacher Employee Retirement Benefit Board</span><br/>
                        <span style="text-align: center; font-size: 14px;">Education Ministry</span><br/>
                        <span style="text-align: center; font-weight: bold; font-size: 14px;">Bank Reconciliation Statement ({{date('d/m/Y', strtotime($start))}} to {{date('d/m/Y', strtotime($end))}})</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" style=" white-space: pre-line;">Account Name: {{$account->name}}</td>
                    @if($account->id != 49)
                        <td></td>
                    @endif
                    <td class="text-center">Account No.: {{$account->account_no}}<br/>{{$account->bankName}}
                        , {{$account->branchName}}, Dhaka.
                    </td>
                </tr>
                <tr>
                    <th style="text-align: center; font-weight: bold;">Particulars</th>
                    @if($account->id != 49)
                        <td style="text-align: center; font-weight: bold;">Beneficiary Count</td>
                    @endif
                    <th style="text-align: center; font-weight: bold;">Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="{{$account->id == 49 ? 1 : 2}}">
                        Cash book Closing ({{date('d/m/Y',strtotime($end))}})
                    </td>
                    <td style="width: 250px; text-align: right;">{{number_format(($balanceUntilDate - $pending_amount - $tranpendAmount - $returnDateItems->sum('amount') - $hold_amount),2)}}</td>
                </tr>

                @if($account->id != 49)

                    <tr>
                        @php
                            $closingBalance += $unpaidAmount;
                        @endphp
                        <td>Paid</td>
                        <td style="text-align: right;">{{$paid_count}}</td>
                        <td style="width: 250px; text-align: right;">{{number_format(($paid_amount),2)}}</td>
                    </tr>
                    <tr>
                        @php
                            $closingBalance += $unpaidAmount;
                        @endphp
                        <td>Pending</td>
                        <td style="text-align: right;">{{($pending_count + $tranpendCount) - $pend_stop_count}}</td>
                        <td style="text-align: right;">{{number_format((($pending_amount + $tranpendAmount) - $pend_stop_amnt), 2)}}</td>
                    </tr>
                    <tr>
                        @php
                            $closingBalance += $unpaidAmount;
                        @endphp
                        <td>Returned</td>
                        <td style="text-align: right;">{{$return_count}}</td>
                        <td style="text-align: right;">{{number_format($return_amount, 2)}}</td>
                    </tr>
                    <tr>
                        @php
                            $closingBalance += $unpaidAmount;
                        @endphp
                        <td>Hold</td>
                        <td style="text-align: right;">{{$hold_count}}</td>
                        <td style="text-align: right;">{{number_format($hold_amount, 2)}}</td>
                    </tr>
                    <tr>
                        @php
                            $closingBalance += $unpaidAmount;
                        @endphp
                        <td>Stop</td>
                        <td style="text-align: right;">{{$stop_count}}</td>
                        <td style="text-align: right;">{{number_format($stop_amount, 2)}}</td>
                    </tr>
                    <tr>
                        @php
                            $closingBalance += $unpaidAmount;
                        @endphp
                        <td colspan="2" style="font-weight: bold;">Total Unpaid Amount</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format(($pending_amount + $returnDateItems->sum('amount') + $hold_amount), 2)}}</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="width: 250px; font-weight: bold;">Total Expanse</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format(($balanceUntilDate - $returnDateItems->sum('amount')), 2)}}</td>
                    </tr>
                    <tr>
                        @php
                            $closingBalance -= $holdAmount;
                        @endphp
                        <td colspan="2">Correction</td>
                        <td style="width: 250px; text-align: right;">{{number_format($holdAmount, 2)}}</td>
                    </tr>

                @else

                    @php
                        $balanceUntilDate = $balanceUntilDate + $unpaidChequeAmount;
                    @endphp

                    <tr>
                        <td>Unpaid Cheque</td>
                        <td class="text-right">{{number_format($unpaidChequeAmount, 2)}}</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="{{$account->id == 49 ? 1 : 2}}" style="font-weight: bold; line-height: 1.5; height: 16px;">Bank
                        Statement Closing ({{$end}})
                    </td>

                    <td style="width: 250px; text-align: right; font-weight: bold;">{{number_format($balanceUntilDate, 2)}}</td>
                </tr>

                @if($account->id == 49)

                    <tr>
                        <td>Cash In Hand</td>
                        <td class="text-right">{{number_format($cashbookAmount, 2)}}</td>
                    </tr>

                    <tr>
                        <td>Net balance</td>
                        <td class="text-right">{{number_format($balanceUntilDate + $cashbookAmount, 2)}}</td>
                    </tr>
                @endif

                </tbody>
            </table>
        </div>
    </div>
</div>
