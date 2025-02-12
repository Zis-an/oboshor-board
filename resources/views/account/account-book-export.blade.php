@extends('layouts.print')

@section('content')

    <div class="card-body">

        <div>

            <div class="bn-font head_title">বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড, শিক্ষা
                মন্ত্রণালয়
            </div>
            <div class="bn-font head_title">হিসাব বিবরণী</div>
            @if(!$account->is_cash_account)
                <div style="text-align: center;"><span class="bn-font">হিসাব নং:</span> {{$account->account_no}}</div>
                <div style="text-align: center;"><span class="bn-font">ব্যাংকের নাম</span>: {{$bank_name->name}}
                    , {{$bank_branch->name}} Branch
                </div>
            @else
                <div style="text-align: center;"><span class="bn-font">পেটি ক্যাশ</span></div>
            @endif
            <div style="text-align: center; margin-bottom: 15px;"><span
                    class="bn-font">সময় কালঃ</span> {{date('d/m/Y', strtotime($startDate))}}
                to {{date('d/m/Y', strtotime($endDate))}}</div>

            @php

                $incBalance = $openingBalance;
                $transactionCount = count($transactions);

            @endphp


            <table class="table">
                <thead>
                <tr>
                    <th class="bn-font">ক্রমিক নং</th>
                    <th class="bn-font">তারিখ</th>
                    <th class="bn-font">বিবরণ</th>
                    <th class="bn-font">চেক নং</th>
                    <th class="bn-font">ডেবিট</th>
                    <th class="bn-font">ক্রেডিট</th>
                    <th class="bn-font">সর্বশেষ স্থিতি</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="bn-font">প্রারম্ভিক স্থিতি</td>
                    <td></td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{number_format($openingBalance, 2)}}</td>
                </tr>
                @foreach($transactions as $index=>$transaction)

                    @php
                        if($transaction->account_type == 'credit'){
                            $incBalance += $transaction->amount;
                        }else{
                            $incBalance -= $transaction->amount;
                        }
                    @endphp

                    <tr>
                        <td class="text-center" style=" width: 7%">{{++$index}}</td>
                        <td>{{\Carbon\Carbon::parse($transaction->date)->format('d-m-Y')}}</td>
                        @if(strlen($transaction->description) == strlen(utf8_decode($transaction->description)))
                            <td>{{$transaction->description}}</td>
                        @else
                            <td class="bn-font">{{$transaction->description}}</td>
                        @endif
                        <td class="text-center">
                            @if($transaction->cheque_number)
                                {{$transaction->cheque_number}}
                            @elseif(count($transaction->cheques) > 0)
                                {{$transaction->cheques->pluck("number")->join(',')}}
                            @endif
                        </td>
                        @if($transaction->account_type == 'debit')
                            <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                            <td class="text-right">-</td>
                        @else
                            <td class="text-right">-</td>
                            <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                        @endif
                        <td class="text-right">{{number_format($incBalance, 2)}}</td>
                    </tr>

                @endforeach

                @foreach($transactionWithIncompleteCheques as $cheque)

                    @php
                        //$totalChequeAmount = $transaction->cheques->sum('amount');
                        $incBalance += $cheque->amount;
                        $incIndex = $loop->iteration + $transactionCount;
                    @endphp

                    <tr>
                        <td class="text-center" style=" width: 7%">{{$incIndex}}</td>
                        <td>{{\Carbon\Carbon::parse($cheque->issue_date)->format('d-m-Y')}}</td>
                        <td>Cheque issued ({{\Carbon\Carbon::parse($cheque->issue_date)->format('d-m-Y')}}), unpaid</td>
                        <td class="text-center">
                            {{$cheque->number}}
                        </td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{number_format($cheque->amount, 2)}}</td>

                        <td class="text-right">{{number_format($incBalance, 2)}}</td>
                    </tr>
                @endforeach

                @foreach($completedWithinDate as $cheque)

                    @php
                        //$totalChequeAmount = $transaction->cheques->sum('amount');
                        $incBalance -= $cheque->amount;
                        $incompleteCount = count($transactionWithIncompleteCheques);
                        $incIndex = $loop->iteration + $transactionCount + $incompleteCount;
                    @endphp

                    <tr>
                        <td class="text-center" style=" width: 7%">{{$incIndex}}</td>
                        <td>{{\Carbon\Carbon::parse($cheque->transaction_completed_date)->format('d-m-Y')}}</td>
                        <td>Due Cheque ({{\Carbon\Carbon::parse($cheque->issue_date)->format('d-m-Y')}}), Payment</td>
                        <td class="text-center">
                            {{$cheque->number}}
                        </td>
                        <td class="text-right">{{number_format($cheque->amount, 2)}}</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{number_format($incBalance, 2)}}</td>
                    </tr>
                @endforeach


                </tbody>
            </table>
        </div>
    </div>

@endsection
