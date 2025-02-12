<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Lot Wise Report</h3>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>
    @php
        $totalSentCount = 0;
        $totalSentAmount = 0;
        $totalPendingCount = 0;
        $totalPendingAmount = 0;
        $totalReturnedCount = 0;
        $totalReturnedAmount = 0;
        $totalHoldCount = 0;
        $totalHoldAmount = 0;
        $totalUnpaidCount = 0;
        $totalUnpaidAmount = 0;
    @endphp
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th rowspan="2">Bank</th>
                <th rowspan="2">Account no.</th>
                <th colspan="2">Paid</th>
                <th colspan="2">Pending</th>
                <th colspan="2">Return</th>
                <th colspan="2">Hold</th>
                <th colspan="2">UnPaid</th>
            </tr>
            <tr>
                <th>Total No.</th>
                <th>Total Amount</th>
                <th>Total No.</th>
                <th>Total Amount</th>
                <th>Total No.</th>
                <th>Total Amount</th>
                <th>Total No.</th>
                <th>Total Amount</th>
                <th>Total No.</th>
                <th>Total Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $account)
                @php
                    $sentCount = $account->lotItems->where('status', 'sent')->count('id');
                    $sentAmount = $account->lotItems->where('status', 'sent')->sum('amount');
                    $pendingCount = $account->lotItems->where('status', 'processing')->count('id');
                    $pendingAmount = $account->lotItems->where('status', 'processing')->sum('amount');
                    $returnedCount = $account->lotItems->where('status', 'returned')->count('id');
                    $returnedAmount = $account->lotItems->where('status', 'returned')->sum('amount');
                    $holdCount = $account->lotItems->where('status', 'hold')->count('id');
                    $holdAmount = $account->lotItems->where('status', 'hold')->sum('amount');
                    $unpaidCount = $holdCount + $returnedCount + $pendingCount;
                    $unpaidAmount = $pendingAmount + $holdAmount + $returnedAmount;

                    $totalSentCount += $sentCount;
                    $totalSentAmount += $sentAmount;
                    $totalReturnedCount += $returnedCount;
                    $totalReturnedAmount += $returnedAmount;
                    $totalPendingCount += $pendingCount;
                    $totalPendingAmount += $pendingAmount;
                    $totalHoldCount += $holdCount;
                    $totalHoldAmount += $holdAmount;
                    $totalUnpaidCount += $unpaidCount;
                    $totalUnpaidAmount += $unpaidAmount

                @endphp
                <tr>
                    <td>{{$account->name}}</td>
                    <td>{{$account->account_no}}</td>
                    <td>{{$sentCount}}</td>
                    <td>{{$sentAmount}}</td>
                    <td>{{$pendingCount}}</td>
                    <td>{{$pendingAmount}}</td>
                    <td>{{$returnedCount}}</td>
                    <td>{{$returnedAmount}}</td>
                    <td>{{$holdCount}}</td>
                    <td>{{$holdAmount}}</td>
                    <td>{{$unpaidCount}}</td>
                    <td>{{$unpaidAmount}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td>Total</td>
                <td class="text-right">{{number_format($totalSentCount, 2)}}</td>
                <td class="text-right">{{number_format($totalSentAmount, 2)}}</td>
                <td class="text-right">{{number_format($totalPendingCount, 2)}}</td>
                <td class="text-right">{{number_format($totalPendingAmount, 2)}}</td>
                <td class="text-right">{{number_format($totalReturnedCount, 2)}}</td>
                <td class="text-right">{{number_format($totalReturnedAmount. 2)}}</td>
                <td class="text-right">{{number_format($totalHoldCount, 2)}}</td>
                <td class="text-right">{{number_format($totalHoldAmount, 2)}}</td>
                <td class="text-right">{{number_format($totalUnpaidCount, 2)}}</td>
                <td class="text-right">{{number_format($totalUnpaidAmount,2)}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
