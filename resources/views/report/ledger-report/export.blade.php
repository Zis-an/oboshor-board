@extends('layouts.print')
@section('content')
    <div>
        @php
            $initialBudget = $budgetAmount;
            $decBalance = $budgetAmount;
            $totalDebit = 0;
            $totalCredit = 0;
            $sln = 1;
        @endphp

        <div class="card-body">

            <div style="margin-top:10px ">

                <div class="bn-font head_title" >বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
                <div class="bn-font head_txt" >শিক্ষা মন্ত্রণালয়</div>
                <div class="bn-font head-title"
                      style="text-align: center; font-weight: bold; font-size: 16px;">{{$head->name ?? ''}}</div>
                @if(!empty($subHead))
                <div class="bn-font head-title"
                     style="text-align: center; font-weight: bold; font-size: 16px;">{{$subHead->name ?? ''}}</div>
                @endif

                <div class="head-body" style="text-align: center"> <span class="bn-font"> সময়কালঃ  </span>{{$startDate}} - {{$endDate}}</div>

            </div>

            <table class="table table-bordered">
                <thead>
                <tr>
                    {{--<th style="width: 40px; font-weight: bold;">SL</th>
                    <th style="width: 80px; font-weight: bold;">Date</th>
                    <th style="width: 140px; font-weight: bold;">Particulars</th>
                    <th style="width: 40px; font-weight: bold;">File P</th>
                    <th style="width: 80px; font-weight: bold;">Lot Name</th>
                    <th style="width: 60px; font-weight: bold;">Index</th>
                    <th style="width: 100px; font-weight: bold;">Debit</th>
                    <th style="width: 100px; font-weight: bold;">Credit</th>
                    <th style="width: 100px; font-weight: bold;">Balance</th>--}}

                    <th style="width: 40px; font-weight: bold;">SL</th>
                    <th style="width: 90px; font-weight: bold;">Date</th>
                    <th style="width: 180px; font-weight: bold;">Particulars</th>
                    <th style="width: 40px; font-weight: bold;">Voucher No</th>
                    <th style="width: 70px; font-weight: bold;">File</th>
                    <th style="width: 40px; font-weight: bold;">Cheque No.</th>
                    <th style="width: 120px; font-weight: bold;">Debit</th>
                    <th style="width: 120px; font-weight: bold;">Credit</th>
                    <th style="width: 120px; font-weight: bold;">Balance</th>

                </tr>
                </thead>
                <tbody>

                <tr>
                    <td class="text-center">{{$sln}}</td>
                    <td>{{$financialYear->name}}</td>
                    <td>Budget</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{number_format($initialBudget,2)}}</td>
                </tr>
                @php $i=0 @endphp
                @foreach($transactions as $transaction)
                    <tr>
                        <td class="text-center">{{++$sln}}</td>
                        <td>{{date('d/m/Y', strtotime($transaction->date))}}</td>
                        @if(strlen($transaction->description) == strlen(utf8_decode($transaction->description)))
                        <td>{{$transaction->description}}</td>
                        @else
                        <td class="bn-font">{{$transaction->description}}</td>
                        @endif
                        <td>
                            {{$transaction->voucher_no}}
                        </td>
                        <td>{{$transaction->file_no}}</td>

                        <td>
                            {{$transaction->cheque_number ?? $transaction->method}}
                        </td>

                        @if($transaction->account_type == 'credit')
                            @php
                                $decBalance += $transaction->amount;
                                $totalCredit += $transaction->amount;
                            @endphp
                            <td class="text-right">-</td>
                            <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                        @else
                            @php
                                $decBalance -= $transaction->amount;
                                $totalDebit += $transaction->amount;
                            @endphp
                            <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                            <td class="text-right">-</td>
                        @endif
                        <td class="text-right">{{number_format($decBalance, 2)}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" style="font-weight: bold; text-align: right;">Total:</td>
                    <td></td>
                    <td style="font-weight: bold; text-align: right;">{{number_format( $totalDebit,2 )}}</td>
                    <td style="font-weight: bold; text-align: right;">{{number_format($totalCredit, 2)}}</td>
                    <td style="font-weight: bold; text-align: right;">{{ number_format($decBalance,2)}}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection


