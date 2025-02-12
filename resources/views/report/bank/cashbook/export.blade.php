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
        $totalCredit = $openingBalance;
        $totalDebit = 0;
        $incBalance = $openingBalance;
        $sln = 1;
    @endphp

    <div class="card-body">
        <div class="bn-font head_title">বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
        <div class="bn-font head_txt">শিক্ষা মন্ত্রণালয়</div>
        @if($accounts_details->is_cash_account)
            <div class="bn-font head_title">পেটি ক্যাশ রিপোর্ট</div>
        @else
            <div class="bn-font head_title">ব্যাংক হিসাব বিবরণী</div>
            <div class="head_body">{{$accounts_details->bankName}} ({{$accounts_details->account_no}}
                ), {{$accounts_details->branchName}}</div>
        @endif

        @if($start != $end)
            <div class="head_body"><span class="font-weight-bold bn-font">সময়কালঃ</span> {{date('d/m/Y', strtotime($start))}} - {{date('d/m/Y', strtotime($end))}}</div>
        @endif

        <table class="table table-bordered" style=" margin-top: 20px;">
            <thead>
            <tr>
                <th>SL</th>
                <th>Date</th>
                @if($accounts_details->is_cash_account)
                <th>Title</th>
                @endif
                <th>Particulars</th>
                <!--<th>File P</th>-->
                @if(!$accounts_details->is_cash_account)
                    <th>Lot</th>
                    <th>Index</th>
                @endif
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <td style="text-align: center;">{{$sln}}</td>
                <td>{{date('d/m/Y', strtotime($start))}}</td>
                @if($accounts_details->is_cash_account)
                <td></td>
                @endif
                <td>Opening Balance</td>
                @if(!$accounts_details->is_cash_account)
                    <td></td>
                    <td></td>
                    <td></td>
                @endif

                <td style=" text-align: right;"></td>
                <td style=" text-align: right;"></td>
                <td style=" text-align: right;">{{number_format($openingBalance, 2)}}</td>
            </tr>
            @php $i=0 @endphp
            @foreach($transactions as $transaction)
            
                <tr>
                    <td style="text-align: center;">{{++$sln}}</td>
                    <td>{{date('d/m/Y', strtotime($transaction->date))}}</td>
                    {{--@if($transaction->lot_item_id)
                        <td>BEFTN Payment</td>
                    @else--}}
                        @php
                            $description = $transaction->description;
                            $headName = $transaction->head ?? '';
                            $headItem = $transaction->headItem->name ?? '';

                            if($transaction->type == 'expense' && !empty($transaction->expenseItems)){
                                $headName = $transaction->expenseItems->pluck('head.name')->join(', ');
                                $headItem = $transaction->expenseItems->pluck('headItem.name')->join(',');
                            }

                        @endphp
                        @if($accounts_details->is_cash_account)
                        <td>
                            @if($headItem)
                                <span class="{{is_unicode($headItem) ? 'bn-font' : ''}}">{{substr($headItem, 0, 25)}}...</span>
                                @elseif($headName)
                                    <span class="{{is_unicode($headName) ? 'bn-font' : ''}}">{{substr($headName, 0, 25)}}...</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        @endif

                        {{--@if(!empty($headName) && !empty($headItem))
                            @php
                                $description = $headName . ' (' .$headItem. ')';
                            @endphp
                        @else
                            @php
                                $description = $headName ?? $headItem ?? $transaction->description;
                            @endphp
                        @endif--}}

                    <td class="{{is_unicode($description) ? 'bn-font' : ''}}">{{substr($description, 0, 12)}}...</td>

                    {{--@endif--}}
                    {{--<td style="text-align: center;">{{$transaction->file_page}}</td>--}}
                    {{--<td style="text-align: center;">{{$transaction->short_name}}</td>--}}
                    @if(!$accounts_details->is_cash_account)
                        <td>{{$transaction->short_name ?? ''}}</td>
                        <td style="text-align: center;">
                            @if($transaction->index)
                                @php $i++ @endphp
                                {{$transaction->index}}
                            @else
                                -
                            @endif
                        </td>
                    @endif

                    @if($transaction->account_type == 'credit')
                        @php
                            $totalCredit += $transaction->amount;
                            $incBalance += $transaction->amount
                        @endphp
                        <td style='text-align: right;'>-</td>
                        <td style='text-align: right;'>{{number_format($transaction->amount,2)}}</td>

                    @else
                        @php
                            $totalDebit += $transaction->amount;
                            $incBalance -= $transaction->amount
                        @endphp

                        <td style='text-align: right;'>{{number_format($transaction->amount, 2)}}</td>
                        <td style='text-align: right;'>-</td>
                    @endif
                    <td style='text-align: right;'>{{number_format($incBalance, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td style=" font-weight: bold; text-align: center;">{{++$sln}}</td>
                <td style=" font-weight: bold;"></td>
                @if($accounts_details->is_cash_account)
                <td style=" font-weight: bold; text-align: right;"></td>
                @endif
                @if(!$accounts_details->is_cash_account)
                    <td style="font-weight: bold; text-align: center;">-</td>
                    <td style="font-weight: bold; text-align: center;">-</td>
                @endif
                <td>Total</td>
                <td style='text-align: right; font-weight: bold;'>{{number_format($totalDebit, 2)}}</td>
                <td style='text-align: right; font-weight: bold;'>{{number_format($totalCredit, 2)}}</td>
                <td style='text-align: right; font-weight: bold;'>{{number_format($incBalance, 2)}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div style="width: 100%; margin-top: 80px;" class="bn-font footer_name">
    <div style=" width: 25%; text-align: center; float: right;"><strong>(শান্তি সরকার)</strong><br/>একাউন্টস অফিসার<br/>অবসর
        সুবিধা বোর্ড
    </div>
</div>

</body>
<script type="text/php">
    if ( isset($pdf) ) {
    $pdf->page_script('
    $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
    $size = 12;
    $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
    $y = 15;
    $x = 520;
    $pdf->text($x, $y, $pageText, $font, $size);
    ');
    }
</script>
</html>


