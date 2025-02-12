@extends('layouts.print')

@section('content')

    <div>

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
            $totalCount = 0;
            $totalAmount = 0;
        @endphp
        <div>

            <div style="margin-top:10px ">

                <div class="bn-font head_title">বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
                <div class="bn-font head_txt">শিক্ষা মন্ত্রণালয়</div>
                <div class="bn-font head-title"
                     style="text-align: center; font-weight: bold; font-size: 20px;">লট রিপোর্ট
                </div>
                @if(!empty($account))
                    <div class="head_body">
                        <strong class="bn-font">ব্যাংক একাউন্টঃ </strong>
                        {{$account->account_no}}</div>
                @endif

            </div>

            <table class="table table-bordered" id="lot_table">
                <thead>
                <tr>
                    <th rowspan="2">Lot Name</th>
                    <th colspan="2">Total</th>
                    <th colspan="2">Paid</th>
                    <th colspan="2">Return</th>
                    {{--<th colspan="2">Pending</th>
                    <th colspan="2">Hold</th>--}}
                    <th colspan="2">UnPaid</th>
                </tr>
                <tr>
                    <th>Total No.</th>
                    <th>Total Amount</th>
                    <th>Total Amount</th>
                    <th>Total No.</th>
                    <th>Total No.</th>
                    <th>Total Amount</th>
                    {{--<th>Total No.</th>
                    <th>Total Amount</th>
                    <th>Total No.</th>
                    <th>Total Amount</th>--}}
                    <th>Total No.</th>
                    <th>Total Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($lots as $lot)
                    @php
                        $sentCount = $lot->items->where('status', 'sent')->count('id');
                        $sentAmount = $lot->items->where('status', 'sent')->sum('amount');
                        $pendingCount = $lot->items->where('status', 'processing')->count('id');
                        $pendingAmount = $lot->items->where('status', 'processing')->sum('amount');
                        $returnedCount = $lot->items->where('status', 'returned')->count('id');
                        $returnedAmount = $lot->items->where('status', 'returned')->sum('amount');
                        $holdCount = $lot->items->where('status', 'hold')->count('id');
                        $holdAmount = $lot->items->where('status', 'hold')->sum('amount');
                        $unpaidCount = $holdCount + $returnedCount + $pendingCount;
                        $unpaidAmount = $pendingAmount + $holdAmount + $returnedAmount;

                        $itemCount = $lot->items()->count('id');
                        $itemAmount = $lot->items->sum('amount');

                        $totalSentCount += $sentCount;
                        $totalSentAmount += $sentAmount;
                        $totalReturnedCount += $returnedCount;
                        $totalReturnedAmount += $returnedAmount;
                        $totalPendingCount += $pendingCount;
                        $totalPendingAmount += $pendingAmount;
                        $totalHoldCount += $holdCount;
                        $totalHoldAmount += $holdAmount;
                        $totalUnpaidCount += $unpaidCount;
                        $totalUnpaidAmount += $unpaidAmount;
                        $totalCount += $itemCount;
                        $totalAmount += $itemAmount;

                    @endphp
                    <tr>
                        <td class="{{is_unicode($lot->name) ? 'bn-font': ''}}">
                            {{$lot->name}}
                        </td>
                        <td class="text-right">{{number_format($itemCount)}}</td>
                        <td class="text-right">{{number_format($itemAmount, 2)}}</td>
                        <td class="text-right">{{number_format($sentCount)}}</td>
                        <td class="text-right">{{number_format($sentAmount,2)}}</td>
                        {{--<td class="text-right">{{$pendingCount}}</td>
                        <td class="text-right">{{$pendingAmount}}</td>--}}
                        <td class="text-right">{{number_format($returnedCount)}}</td>
                        <td class="text-right">{{number_format($returnedAmount,2)}}</td>
                        {{--<td class="text-right">{{$holdCount}}</td>
                        <td class="text-right">{{$holdAmount}}</td>--}}
                        <td class="text-right">{{number_format($unpaidCount)}}</td>
                        <td class="text-right">{{number_format($unpaidAmount,2)}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="text-right">{{number_format($totalCount, 2)}}</td>
                    <td class="text-right">{{number_format($totalAmount, 2)}}</td>
                    <td class="text-right">{{number_format($totalSentCount, 2)}}</td>
                    <td class="text-right">{{number_format($totalSentAmount, 2)}}</td>
                    {{--<td class="text-right">{{number_format($totalPendingCount, 2)}}</td>
                    <td class="text-right">{{number_format($totalPendingAmount, 2)}}</td>--}}
                    <td class="text-right">{{number_format($totalReturnedCount, 2)}}</td>
                    <td class="text-right">{{number_format($totalReturnedAmount. 2)}}</td>
                    {{--<td class="text-right">{{number_format($totalHoldCount, 2)}}</td>
                    <td class="text-right">{{number_format($totalHoldAmount, 2)}}</td>--}}
                    <td class="text-right">{{number_format($totalUnpaidCount, 2)}}</td>
                    <td class="text-right">{{number_format($totalUnpaidAmount,2)}}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection
