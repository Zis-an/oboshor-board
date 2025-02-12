@extends('layouts.print')

@section('content')
<div>
    @php
    $totalBudget = 0;
    $totalExpense = 0;
    $sln = 1;
    @endphp

    <div class="card-body">

        <div class="bn-font head_title" >বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড </div>
        <div class="bn-font head_txt" >শিক্ষা মন্ত্রণালয়</div>
        <div class="bn-font head_title" ><span class="en-font">{{$financialYear->name}}</span> অর্থ বছরের ব্যয় বিবরণী</div>

        <table class="table table-bordered">
            {{--<tr>
            <td colspan="9" style="text-align: center;">
                <span style="text-align: center; font-weight: bold!important; font-size: 14px!important;">Non Government Teacher Employee Retirement Benefit Board</span><br/>
                    <span style="text-align: center; font-size: 14px;">Education Ministry</span><br/>
                    <span
                        style="text-align: center; font-size: 14px;">Financial Year: {{$financialYear->name}}</span><br/>
            </td>
            </tr>--}}
            <thead>
                <tr>
                    <th class="bn-font">ক্রমিক নং</th>
                    <th class="bn-font">বিবরণ</th>
                    <th class="bn-font">প্রস্তাবিত ব্যয়</th>
                    <th class="bn-font">প্রকৃত ব্যয়</th>
                    <th class="bn-font">অব্যয়িত অর্থ</th>
                </tr>
            </thead>
            <tbody>

                @php
                $headTitle = '';
                $headBudget = 0;
                $headExpense = 0;
                $total_budget = 0;
                $total_expenses = 0;
                @endphp

                @foreach($budgetItems as $key=>$budgetItem)

                {{--<tr>
                    {{$budgetItem->headTransactions}}
                </tr>--}}

                {{--<tr>
            <td>{{$budgetItem->headTransactions->sum('amount')}}</td>
                    </tr>--}}

       <tr>
                <td>{{$key}}</td>
                @if($budgetItem->parent_id)
                <td class="bn-font">{{$budgetItem->headItem->name ?? ''}}</td>
                @php
                $headBudget += $budgetItem->amount;

                $expenseAmount = $budgetItem->transactions->sum('amount') ?? 0;
                if($budgetItem->head_item_id == 1){
                $expenseAmount = $totalTransactionAmountThoseHaveLot;
                }

                $headExpense += $expenseAmount;
                $totalBudget += $budgetItem->amount;
                $totalExpense += $budgetItem->transactions->sum('amount');
                @endphp
                @else
                @php
                $headTitle = $budgetItem->head->name;
                $headBudget = 0;
                $headExpense = 0;
                @endphp
                <td class="font-weight-bold bn-font">{{$budgetItem->head->name ?? ''}}</td>
                @endif
                @if((!empty($budgetItems[$key + 1]) && $budgetItems[$key + 1]->parent_id == $budgetItem->id))
                <td></td>
                <td></td>
                @else
                @php
                $amount = $budgetItem->transactions->sum('amount') ?? 0;
                if($budgetItem->head_item_id == 1){
                $amount = $totalTransactionAmountThoseHaveLot;
                }
                @endphp
                <td class="text-right">{{number_format($budgetItem->amount ?? 0, 2)}}</td>
                <td class="text-right">{{number_format($amount, 2)}}</td>
                <td class="text-right">{{(number_format($budgetItem->amount - $amount, 2))}}</td>
                @endif

            </tr>

            {{--<tr>
            <td>{{$budgetItem->transactions}}</td>
                    </tr>--}}

       @if($budgetItem->parent_id)
                    @if((!empty($budgetItems[$key + 1]) && empty($budgetItems[$key+1]->parent_id)) || (empty($budgetItems[$key+1]->parent_id) || empty($budgetItems[$key+1])))
                    @php
                    $total_budget = $total_budget + $headBudget;
                    $total_expenses = $total_expenses + $headExpense;
                    @endphp
        
                    <tr>
                <td></td>
                <td class="font-weight-bold">Sub Total <span class="bn-font">({{$headTitle}})</span></td>
                <td class="text-right font-weight-bold">{{number_format($headBudget, 2)}}</td>
                <td class="text-right font-weight-bold">{{number_format($headExpense, 2)}}</td>
                <td class="text-right font-weight-bold">{{number_format($headBudget - $headExpense, 2)}}</td>
            </tr>
            @endif
            @endif

            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td style="font-weight: bold; text-align: right;" class="bn-font">
                        সর্বমোট
                    </td>
                    <td style="font-weight: bold; text-align: right;">{{number_format( $total_budget,2 )}}</td>
                    <td style="font-weight: bold; text-align: right;">{{number_format($total_expenses, 2)}}</td>
                    <td style="font-weight: bold; text-align: right;">{{number_format($total_budget - $total_expenses, 2)}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection


