<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>পেন্ডিং ইনডেক্স বিবরণী</title>
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

            .text-right{
                text-align: right;
            }

            .font-weight-bold {
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            th{
                background-color: #333333;
                color: #FFF;
                font-size: 1.3em;
                text-align: center;
            }
            .head_title{
                font-size: 1.6em;
                text-align: center;
            }
            .head_txt{
                font-size: 1.5em;
                text-align: center;
            }
            .footer_name{
                font-size: 1.2em;
                text-align: center;
            }
            td{
                font-size: 1.2em;
            }
            .head_body{
                font-size: 1.3em;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div>

            <div class="card-body">

                <div class="bn-font head_title" >বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
                <div class="bn-font head_txt" >শিক্ষা মন্ত্রণালয়</div>
                <div class="bn-font head_title" >পেন্ডিং ইনডেক্স বিবরণী</div>
                <div class="head_body" >{{$account->bankName}} ({{$account->account_no}}), {{$account->branchName}}</div>

                <table class="table table-bordered" style=" margin-top: 20px;">
                    <thead>

                        <tr>
                            <th class="text-center">SL</th>
                            <th>Date</th>
                            <th>Lot Name</th>
                            <th>Index</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                        $count = 0; 
                        $amount_val = 0.00;
                        @endphp
                        @foreach($prossItems as $pross)
                        @php 
                        $amount_val = $amount_val + $pross->amount;
                        @endphp

                        <tr>
                            <td class="text-center">{{++$count}}</td>
                            <td>{{date('d/m/Y', strtotime($pross->date))}}</td>
                            <td>{{$pross->short_name}}</td>
                            <td style="text-align: center;">{{$pross->index}}</td>
                            <td style="text-align: right;">{{number_format($pross->amount, 2)}}</td>
                        </tr>
                        @endforeach

                        @foreach($transac as $val)
                        @php 
                        $amount_val = $amount_val + $val->amount;
                        @endphp

                        <tr>
                            <td class="text-center">{{++$count}}</td>
                            <td>{{date('d/m/Y', strtotime($val->date))}}</td>
                            <td>{{$val->short_name}}</td>
                            <td style="text-align: center;">{{$val->index}}</td>
                            <td style="text-align: right;">{{number_format($val->amount, 2)}}</td>
                        </tr>
                        @endforeach

                        @foreach($items as $item)

                        @php
                        $trans = \App\Models\Transaction::where('lot_item_id', $item->indexId)->orderBy('date', 'DESC')->first();
                        @endphp

                        @if($trans->account_type == 'credit' && $trans->date <= $end)

                        @php 
                        $count++;
                        $amount_val = $amount_val + $item->amount;
                        @endphp

<!--                <tr>
                    <td>{{$item->date}}</td>
                    <td>{{$item->short_name}}</td>
                    <td style="text-align: center;">{{$item->index}}</td>
                    <td style="text-align: right;">{{number_format($item->amount, 2)}}</td>
                </tr>-->
                        @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="font-weight: bold; text-align: right;">Total:</td>
                            <td style="font-weight: bold; text-align: center;">{{count($prossItems) + count($transac)}}</td>
                            <td style="font-weight: bold; text-align: right;">{{number_format(($prossItems->sum('amount') + $transac->sum('amount')), 2)}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </body>
</html>
