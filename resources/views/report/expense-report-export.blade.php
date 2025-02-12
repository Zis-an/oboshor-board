<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>হিসাব বিবরণী</title>
        <style>
            .bn-font {
                font-family: 'solaimanlipi', sans-serif;
            }

            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            .table-bordered, td, th {
                border: 1px solid #939393;
                padding: 7px;
            }

            .table {
                border-collapse: collapse;
                width: 100%;
            }

            .text-capitalize {
                text-transform: capitalize;
            }

            .text-right {
                text-align: right;
            }

            .font-weight-bold {
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            th {
                background-color: #333333;
                color: #FFF;
                font-size: 1.3em;
                text-align: center;
            }

            .head_title {
                font-size: 1.6em;
                text-align: center;
            }

            .head_txt {
                font-size: 1.5em;
                text-align: center;
            }

            .footer_name {
                font-size: 1.2em;
                text-align: center;
            }

            td {
                font-size: 1.2em;
            }

            .head_body {
                font-size: 1.3em;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div>
            @php
            /*$totalCredit = $openingBalance;
            $totalDebit = 0;*/
            $incAmount = 0;
            $sln = 1;
            @endphp

            <div class="card-body">
                <div class="bn-font head_title">বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
                <div class="bn-font head_txt">শিক্ষা মন্ত্রণালয়</div>
                <div class="bn-font head_txt">খরচ রিপোর্ট</div>
                @if($start != $end)
                <div class="head_body"><span
                        class="font-weight-bold bn-font">সময়কালঃ</span> {{date('d/m/Y', strtotime($start))}}
                    - {{date('d/m/Y', strtotime($end))}}</div>
                @endif

                <table class="table table-bordered" style=" margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Particulars</th>
                            <th>Payment Method</th>
                            <th>Cheque No.</th>
                            {{--<th>For</th>--}}
                            <th>Amount</th>
                            <th>Cumulative Amount</th>
                        </tr>
                    </thead>
                    <tbody>



                    @php $i=0 @endphp
                    @foreach($transactions as $index=>$transaction)
                    <tr>
                        <td style="text-align: center;">{{$index + 1}}</td>
                        <td>{{date('d/m/Y', strtotime($transaction->date))}}</td>
                        @php
                        $description = $transaction->description;
                        $headName = $transaction->head ?? '';
                        $headItem = $transaction->headItem->name ?? '';

                        $incAmount += $transaction->amount;

                        if($transaction->type == 'expense' && !empty($transaction->expenseItems)){
                        $headName = $transaction->expenseItems->pluck('head.name')->join(', ');
                        $headItem = $transaction->expenseItems->pluck('headItem.name')->join(',');
                        }

                        @endphp

                        <td>
                            @if($headItem)
                            <span class="{{is_unicode($headItem) ? 'bn-font' : ''}}">{{$headItem}}</span>
                            @elseif($headName)
                            <span class="{{is_unicode($headName) ? 'bn-font' : ''}}">{{$headName}}</span>
                            @else
                            <span>-</span>
                            @endif
                        </td>

                        <td class="{{is_unicode($description) ? 'bn-font' : ''}}">{{$description}}</td>

                        <td>{{$transaction->method}}</td>

                        <td>{{$transaction->cheque_number}}</td>

                        {{--<td>{{$transaction->transactionFor->name ?? ''}}</td>--}}

                        <td style='text-align: right;'>{{number_format($transaction->amount,2)}}</td>
                        <td style='text-align: right;'>{{number_format($incAmount,2)}}</td>

                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            {{-- <td>{{$transactions->count()}}</td>
                            <td style=" font-weight: bold;">{{date('d/m/Y', strtotime($end))}}</td>--}}
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style=" font-weight: bold;" colspan="3">Total</td>

                            <td style='text-align: right; font-weight: bold;'>{{number_format($total, 2)}}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div style="width: 100%; margin-top: 80px;" class="bn-font footer_name">
            <div style=" width: 25%; text-align: center; float: right;"><strong>(শান্তি সরকার)</strong><br/>একাউন্টস
                অফিসার<br/>অবসর
                সুবিধা বোর্ড
            </div>
        </div>
    </body>
</html>


