<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    @php
        $totalBudget = 0;
        $totalExpense = 0;
        $sln = 1;
    @endphp

    <div class="card-body">
        <table class="w-100 table table-bordered" id="account_book_table">
            <thead>
            <tr>
                {{--<th>SL</th>
                <th>Head</th>
                <th>Budget</th>
                <th>Expense</th>
                <th>Remaining</th>--}}
                
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
                    
                    @if($budgetItem->parent_id)
                        <td>{{$budgetItem->headItem->name ?? ''}}</td>
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
                        <td class="font-weight-bold">{{$budgetItem->head->name ?? ''}}</td>
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
<!--                            <td></td>-->
                            <td class="font-weight-bold">Sub Total ({{$headTitle}})</td>
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
                <!--<td></td>-->
                <td style="font-weight: bold; text-align: right;">
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
