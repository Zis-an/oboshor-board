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
            border: 1px solid black;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

    </style>
</head>
<body>
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

<table class="table table-bordered">
    <thead>
    <tr>
        <td colspan="12" class="text-center">Non Government Teacher Employee Retirement Benefit Board</td>
    </tr>
    <tr>
        <td colspan="12" class="text-center">Education Ministry</td>
    </tr>
    <tr>
        <td colspan="12" class="text-center">Bank Report {{$start}} - {{$end}}</td>
    </tr>
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
            $returnedCount = $account->lotItems->where('status', 'sent')->count('id');
            $returnedAmount = $account->lotItems->where('status', 'sent')->sum('amount');
            $holdCount = $account->lotItems->where('status', 'sent')->count('id');
            $holdAmount = $account->lotItems->where('status', 'sent')->sum('amount');
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
            <td class="text-right">{{$sentCount}}</td>
            <td class="text-right">{{$sentAmount}}</td>
            <td class="text-right">{{$pendingCount}}</td>
            <td class="text-right">{{$pendingAmount}}</td>
            <td class="text-right">{{$returnedCount}}</td>
            <td class="text-right">{{$returnedAmount}}</td>
            <td class="text-right">{{$holdCount}}</td>
            <td class="text-right">{{$holdAmount}}</td>
            <td class="text-right">{{$unpaidCount}}</td>
            <td class="text-right">{{$unpaidAmount}}</td>
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
        <td  class="text-right">{{number_format($totalReturnedCount, 2)}}</td>
        <td class="text-right">{{number_format($totalReturnedAmount. 2)}}</td>
        <td class="text-right">{{number_format($totalHoldCount, 2)}}</td>
        <td class="text-right">{{number_format($totalHoldAmount, 2)}}</td>
        <td class="text-right">{{number_format($totalUnpaidCount, 2)}}</td>
        <td class="text-right">{{number_format($totalUnpaidAmount,2)}}</td>
    </tr>
    </tfoot>
</table>
</body>
</html>




