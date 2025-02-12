<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>গত</title>
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
    </style>
</head>
<body>
<div>
    @php
        function format($amount){
        return number_format($amount, 2);
        }

    @endphp

    @php
        $totalOpeningBalance = 0;
        $totalProfit = 0;
        $totalCharge = 0;
        $total25 = 0;
        $total75= 0;
        $total = 0;
    @endphp

    <div class="bn-font head_title">বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
    <div class="bn-font head_txt">শিক্ষা মন্ত্রণালয়</div>
    <div class="bn-font head_title">এফডিআর হিসাব বিবরণী</div>
    <div class="head_title"><span class="bn-font">সময় কালঃ</span> {{$startDate}} - {{$endDate}}</div>
    <table class="table table-bordered" style=" margin-top: 20px;">
        <thead>
        <tr>
            <th class="bn-font">ক্রমিক নং</th>
            <th class="bn-font">হিসেব নং</th>
            <th class="bn-font">ব্যাংকের নাম</th>
            <th class="bn-font">ব্রাঞ্চের নাম</th>
            <th class="bn-font">পূর্বের স্থিতি</th>
            <th class="bn-font">মোট মুনাফার পরিমান</th>
            <th class="bn-font">কর,শুল্ক,ব্যাংক চার্জ</th>
            <th class="bn-font">নেট মুনাফা</th>
            <th class="bn-font">স্থানান্তরিত মুনাফা</th>
            <th class="bn-font">পুনঃবিনিয়োগ মুনাফা</th>
            <th class="bn-font">সর্বশেষ মোট টাকার পরিমাণ</th>
            <th class="bn-font">সুদ হার</th>
            <th class="bn-font">পুনঃ তারিখ</th>
        </tr>
        </thead>
        <tbody>
        <tbody>
        @foreach($accounts as $index=>$account)
            @php
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

            @endphp
            <tr>
                <td class=" text-center">{{$index + 1}}</td>
                <td>{{$account->account_no}}</td>
                <td>{{$account->bank->short ?? ''}}</td>
                <td>{{$account->branch->name ?? ''}}</td>
                <td class="text-right">{{number_format($openingBalance, 2)}}</td>
                <td class="text-right">{{number_format($account->totalProfit ?? 0, 2)}}</td>
                <td class="text-right">{{number_format($account->charge ?? 0, 2)}}</td>
                <td class="text-right">{{number_format(($amount75 + $amount25), 2)}}</td>
                <td class="text-right">{{number_format($amount75, 2)}}</td>
                <td class="text-right">{{number_format($amount25, 2)}}</td>
                <td class="text-right">{{number_format($account->end_balance, 2)}}</td>
                <td class="text-right">{{$account->interest_rate}}%</td>
                @if(isset($account->closed_at) && !empty($account->closed_at))
                <td class="text-right">Closed</td>
                @else
                @if(isset($account->end_date) && !empty($account->end_date))
                <td class="text-right">{{date('d/m/Y', strtotime($account->end_date))}}</td>
                @else
                <td></td>
                @endif
                @endif
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="bn-font" style="font-weight: bold; text-align: right;">
                মোট
            </td>
            <td class="text-right">{{number_format($totalOpeningBalance, 2)}}</td>
            <td class="text-right">{{number_format($totalProfit, 2)}}</td>
            <td class="text-right">{{number_format($totalCharge, 2)}}</td>
            <td class="text-right">{{number_format(($total75 + $total25), 2)}}</td>
            <td class="text-right">{{number_format($total75, 2)}}</td>
            <td class="text-right">{{number_format($total25, 2)}}</td>
            <td class="text-right">{{number_format($total, 2)}}</td>
            <td></td>
        </tr>
        </tfoot>
    </table>
    <div style="width: 100%; margin-top: 100px;" class="bn-font footer_name">
        <div style=" width: 25%; text-align: center; float: right;"><strong>(শান্তি সরকার)</strong><br/>একাউন্টস
            অফিসার<br/>অবসর সুবিধা বোর্ড
        </div>
    </div>

</div>

</body>
</html>

