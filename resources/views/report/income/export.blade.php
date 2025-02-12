<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>আয় বিবরণী</title>
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
            <div class="bn-font head_title" >আয় বিবরণী</div>
            <div class="bn-font head_title" >আয়ের ধরনঃ 
                @if(isset($head_item) && !empty($head_item))
                {{$head_item->name}}
                @else
                @if(isset($head_item) && !empty($head_item))
                {{$head_sub_item->name}}
                @else
                সকল
                @endif
                @endif

            </div>

            <div class="head_title" ><span class="bn-font">সময়কালঃ</span> {{date('d/m/Y', strtotime($dateRange[0]))}}  to {{date('d/m/Y', strtotime($dateRange[1]))}}</div> 
            @php $total_account = 0 @endphp
            <table class="table table-bordered" style=" margin-top: 20px;">
                <thead>
                    <tr>
                        <th class="bn-font">#</th>
                        <th class="bn-font">তারিখ</th>
                        <th class="bn-font">ব্যাংকের নাম</th>
                        <th class="bn-font">শাখা</th>
                        <th class="bn-font">হিসাব নং</th>                        
                        <th class="bn-font">বিবরণ</th>
                        <th class="bn-font">টাকার পরিমাণ</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                    $i = 0
                    
                    @endphp
                    
                    @foreach($incomes as $index=>$income)
                    <tr>
                        <td>{{++$i}}</td>
                        <td>{{Carbon\Carbon::parse($income->date)->format('d/m/Y')}}</td>
                        @if(isset($income->transaction_id) && !empty($income->transaction_id))
                        @php
                        $bak_name =App\Models\Transaction::select('accounts.account_no AS bankAccount', 'banks.short AS bankName', 'branches.name AS branchname', 'transactions.description AS tranDesc')
                        ->leftjoin('accounts', 'accounts.id', '=', 'transactions.account_id')
                        ->leftjoin('banks', 'banks.id', '=', 'accounts.bank_id')
                        ->leftjoin('branches', 'branches.id', '=', 'accounts.branch_id')
                        ->where('transactions.id', '=', $income->transaction_id)
                        ->first();
                        $total_account++
                        @endphp
                        @else
                        @php
                        $bak_name =App\Models\Transaction::select('accounts.account_no AS bankAccount', 'banks.short AS bankName', 'branches.name AS branchname', 'transactions.description AS tranDesc')
                        ->leftjoin('accounts', 'accounts.id', '=', 'transactions.account_id')
                        ->leftjoin('banks', 'banks.id', '=', 'accounts.bank_id')
                        ->leftjoin('branches', 'branches.id', '=', 'accounts.branch_id')
                        ->where('transactions.id', '=', $income->id)
                        ->first();
                        $total_account++
                        @endphp
                        @endif
                        <td>{{$bak_name->bankName ?? ''}}</td>
                        <td>{{$bak_name->branchname ?? ''}}</td>
                        <td>{{$bak_name->bankAccount ?? ''}}</td>
                        <td>{{$bak_name->tranDesc ?? ''}}</td>
                        <td style=" text-align: right;">{{number_format($income->amount, 2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class=" text-right"><strong>Total</strong></td>
                        <td><strong>{{number_format($total, 2)}}</strong></td>
                    </tr>
                </tfoot>
            </table>

<!--            <div style="width: 100%; margin-top: 80px;" class="bn-font footer_name">
                <div style=" width: 25%; text-align: center; float: right;"><strong>(শান্তি সরকার)</strong><br />একাউন্টস অফিসার<br />অবসর সুবিধা বোর্ড</div>
            </div>-->
        </div>
    </body>
</html>

