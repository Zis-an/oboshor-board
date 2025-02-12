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
        $totalDeposit = 0;
        $totalWithdraw = 0;
        $totalCharge = 0;
        $total = 0;
        $totalProfit = 0;
    @endphp

    <div class="bn-font head_title">বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
    <div class="bn-font head_txt">শিক্ষা মন্ত্রণালয়</div>
    <div class="bn-font head_title">এসটিডি হিসাব বিবরণী</div>
    <div class="head_title"><span class="bn-font">সময় কালঃ</span> {{$startDate}} - {{$endDate}}</div>
    <table class="table table-bordered" style=" margin-top: 20px;">
        <thead>
        <tr>
            <th class="bn-font">ক্রমিক নং</th>
            <th class="bn-font">হিসেব নং</th>
            <th class="bn-font">ব্যাংকের নাম</th>
            <th class="bn-font">ব্রাঞ্চের নাম</th>
            <th class="bn-font">প্রারম্ভিক টাকার পরিমান</th>
            <th class="bn-font">মোট জমার পরিমাণ</th>
            <th class="bn-font">মোট খরচের পরিমাণ</th>
            <th class="bn-font">মোট সুদের পরিমাণ</th>
            <th class="bn-font">কর, শুল্ক, ব্যাংক চার্জ</th>
            <th class="bn-font">সর্বশেষ মোট টাকার পরিমান</th>
        </tr>
        </thead>

        <tbody>

        @foreach($accounts as $index=>$account)
            <?php
            ?>
            @php                
                $opening_bal = App\Models\Transaction::where('account_id', $account->id)->where('type', '=', 'opening_balance')->first();
                $openingBalance = $account->blance > 0 ? $account->balance : $account->opening_balance;
                $creditAmount = $account->deposit - $account->withdraw;
                $endBalance = $account->balance + $creditAmount;
                $totalOpeningBalance += $openingBalance;
                $totalDeposit += $account->deposit;
                $totalWithdraw += $account->withdraw;
                $total += $endBalance;
                $totalCharge += $account->charge;
                $totalProfit += $account->profit;
                
                if(isset($opening_bal->amount) && !empty($opening_bal->amount)){
                $openingBalance = $opening_bal->amount;
                }
            @endphp

            <tr>
                <td>{{$index + 1}}</td>
                <td>{{$account->account_no}}</td>
                <td>{{$account->bank->short ?? ''}}</td>
                <td>{{$account->branch->name ?? ''}}</td>
                <td class="text-right">{{number_format($openingBalance ?? 0, 2)}}</td>
                <td class="text-right">{{number_format(($account->deposit - $account->profit - $openingBalance) ?? 0, 2)}}</td>
                <td class="text-right">{{number_format(($account->withdraw - $account->charge) ?? 0, 2)}}</td>
                <td class="text-right">{{number_format($account->profit ?? 0, 2)}}</td>
                <td class="text-right">{{number_format($account->charge ?? 0, 2)}}</td>
                <td class="text-right">{{number_format(($account->deposit - $account->withdraw), 2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="bn-font" style="font-weight: bold; text-align: right;">
                সর্বমোট
            </td>
            <td class="text-right">{{number_format($totalOpeningBalance,2)}}</td>
            <td class="text-right">{{number_format($totalDeposit, 2)}}</td>
            <td class="text-right">{{number_format($totalWithdraw, 2)}}</td>
            <td class="text-right">{{number_format($totalProfit, 2)}}</td>
            <td class="text-right">{{number_format($totalCharge, 2)}}</td>
            <td class="text-right">{{number_format($total, 2)}}</td>
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

