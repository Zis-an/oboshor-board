<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Reconciliation Report</h3>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>
    @php
    $closingBalance = $balanceUntilDate;
    @endphp
    <div class="card-body">
        <table class="table table-bordered" id="lot_hold_table">
            <thead>
            <tr>
                <td style=" font-weight: bold;">Particulars</td>
                <td style=" font-weight: bold;">Beneficiary Count</td>
                <td style=" font-weight: bold;">Amount</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Cash book closing ({{$end}})
                </td>
                <td></td>
                <td style="text-align: right;">{{number_format(($balanceUntilDate - $pending_amount - $tranpendAmount - $returnDateItems->sum('amount') - $hold_amount),2)}}</td>
            </tr>
            <tr>
                <td>
                    Paid
                </td>
                <td style="text-align: right;">{{$paid_count - $returnDateItems->count('index')}}</td>
                <td style="text-align: right;">{{number_format(($paid_amount - $returnDateItems->sum('amount')),2)}}</td>
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
                <td style="text-align: right;">{{$returnDateItems->count('index') - $ret_stop_count}}</td>
                <td style="text-align: right;">{{number_format(($returnDateItems->sum('amount') - $ret_stop_amnt), 2)}}</td>
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
                <td style="font-weight: bold;">Total Unpaid Amount</td>
                <td></td>
                <td style="text-align: right; font-weight: bold;">{{number_format(($pending_amount + $returnDateItems->sum('amount') + $hold_amount), 2)}}</td>
            </tr>
            
            <tr>
                <td>Total Expanse </td>
                <td></td>
                <td style="text-align: right; font-weight: bold;">{{number_format(($balanceUntilDate - $returnDateItems->sum('amount')), 2)}}</td>
            </tr>
            <tr>
                @php
                $closingBalance -= $holdAmount;
                @endphp
                <td>Correction</td>
                <td></td>
                <td style="text-align: right;">{{number_format($holdAmount, 2)}}</td>
            </tr>
            <tr>
                <td>Bank Statement Closing ({{$end}})</td>
                <td></td>
                <td style="text-align: right; font-weight: bold;">{{number_format($balanceUntilDate, 2)}}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
