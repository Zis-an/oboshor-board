<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>শিক্ষক / কর্মচারীদের পেমেন্ট বিবরণী</title>
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
                <div class="bn-font head_title" >শিক্ষক / কর্মচারীদের পেমেন্ট বিবরণী</div>
                <div class="head_body " ><span class="bn-font">সময়কালঃ</span> {{date('d/m/Y', strtotime($start))}} to {{date('d/m/Y', strtotime($end))}}</div>
                <div class="head_body" >{{$account->bankName}} ({{$account->account_no}}), {{$account->branchName}}</div>

                <table class="table table-bordered" style=" margin-top: 20px;">
                    <thead>                
                        <tr>
                            <th>SL</th>
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
                        @if(in_array($pross->index, $returns))
                        @php continue; @endphp
                        @endif
                        @php 
                        $amount_val = $amount_val + $pross->amount;
                        @endphp

                        <tr>
                            <td>{{++$count}}</td>
                            <td>{{date('d/m/Y', strtotime($pross->paymDate))}}</td>
                            <td>{{$pross->lot_name}}</td>
                            <td style="text-align: center;">{{$pross->index}}</td>
                            <td style="text-align: right;">{{number_format($pross->amount, 2)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="font-weight: bold; text-align: right;">Total:</td>
                            <td style="font-weight: bold; text-align: center;">{{$paidItemtotal}}</td>
                            <td style="font-weight: bold; text-align: right;">{{number_format($paidItemAmount, 2)}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </body>
</html>
