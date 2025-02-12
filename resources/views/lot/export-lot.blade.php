<html>
    <head>
        <title>লট বিবরণী</title>
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
        </style>
    </head>
    <body>
        <div>
            <div class="bn-font head_title" >বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
            <div class="bn-font head_txt" >শিক্ষা মন্ত্রণালয়</div>
            <div class="bn-font head_title" >লট বিবরণী</div>
            @if(isset($bank_acc) && !empty($bank_acc))
            <div class="head_title" ><span class="bn-font">ব্যাংক</span>: {{$bank_acc->bank_name}} ({{$bank_acc->account_no}})</div>            
            @else
            <div class="bn-font head_title" >ব্যাংক: সকল</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Lot Name</th>
                        <th>Approved Date</th>
                        <th>Total Amount</th>
                        <th>Total</th>
                        <th>Sent</th>
                        <th>Hold</th>
                        <th>Returned</th>                        
                        <th>Processing</th>
                        <th>Bank</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        @if(strlen($item->name) == strlen(utf8_decode($item->name)))
                        <td>{{$item->name}}</td>
                        @else
                        <td class="bn-font">{{$item->name}}</td>
                        @endif
                        <td class="text-center">{{date('d/m/Y', strtotime($item->approve_date))}}</td>
                        <td class="text-right">{{number_format($item->total_amount, 2)}}</td>
                        <td class="text-center">{{$item->total}}</td>
                        <td class="text-center">{{$item->sent_count}}</td>
                        <td class="text-center">{{$item->hold_count}}</td>
                        <td class="text-center">{{$item->returned_count}}</td>
                        <td class="text-center">{{$item->processing_count}}</td>
                        <td class="text-center">{{$item->bank_Name}}</td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>

    </body>
</html>
