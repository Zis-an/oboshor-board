<div class="card-body" id="budget_container">

    @php
        $totalAmount = 0;
    @endphp

    <div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                @if(!empty($prevFinancialYear))
                    <th style="width: 200px">Budget of {{$prevFinancialYear->name ?? ''}}</th>
                    <th>Actual {{$type}} {{$prevFinancialYear->name}}</th>
                @endif
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($heads as $headIndex=>$head)

                <tr class="sub-total-row">

                    <input type="hidden"
                           name="{{'items['. $headIndex . '][head_id]'}}"
                           value="{{$head->id}}">
                    <td class="text-bold" style="width: 75%">{{$head->name}}</td>
                    @if($type == 'expense' && !empty($prevFinancialYear))
                        <td class="text-right font-weight-bold">
                            {{number_format($head->budget->amount ?? 0, 2)}}
                        </td>
                        @php
                            $headAmount = $head->transactionItems->sum('amount') ?? 0;
                            $totalAmount += $headAmount;
                        @endphp
                        <td class="text-right font-weight-bold"
                            style="width: 200px">{{number_format($headAmount, 2)}}</td>
                    @endif

                    @if($type == 'income' && !empty($prevFinancialYear))
                        @php
                            $headAmount = $head->transactions->sum('amount') ?? 0;
                            $totalAmount += $headAmount;
                        @endphp
                        <td class="text-right font-weight-bold">
                            {{number_format($head->budget->amount ?? 0, 2)}}
                        </td>
                        <td class="text-right font-weight-bold"
                            style="width: 200px">{{number_format($headAmount, 2)}}</td>
                    @endif

                    <td style="width: 25%">
                        @if(isset($head->items ) && count($head->items))
                            {{Form::number('items['.$headIndex. '][amount]', '', ['class' => 'form-control sub-total', 'readonly' => true])}}
                        @else
                            {{Form::text('items['.$headIndex. '][amount]', '', ['class' => 'form-control sub-total', ])}}
                        @endif
                    </td>
                </tr>
                @if(isset($head->items))
                    @foreach( $head->items as $index=>$item)
                        <tr>
                            <input type="hidden"
                                   name="{{'items['. $headIndex . '][child][' . $index.'][head_item_id]'}}"
                                   value="{{$item->id}}">
                            <td style="width: 75%">{{$item->name}}</td>
                            @if($type == 'expense' && !empty($prevFinancialYear))
                                <td class="text-right"> {{number_format($item->budget->amount ?? 0, 2)}}</td>
                                <td class="text-right">{{number_format($item->transactionItems->sum('amount'), 2)}}</td>
                            @endif

                            @if($type == 'income' && !empty($prevFinancialYear))
                                <td class="text-right">
                                    {{number_format($item->budget->amount ?? 0, 2)}}
                                </td>
                                <td class="text-right"
                                    style="width: 200px">{{number_format($item->transactions->sum('amount'), 2)}}</td>
                            @endif

                            <td style="width: 25%">
                                {{Form::text('items['.$headIndex. '][child]['  .$index.'][amount]', '', ['class' => 'form-control td-amount'])}}
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td style="width:75%">Total Amount</td>
                <td>{{number_format($prevBudget->amount, 2)}}</td>
                <td>{{number_format($totalAmount, 2)}}</td>
                <td>
                    {!! Form::number('amount', 0, ['class' => 'form-control', 'id' => 'total-amount', 'readonly']) !!}
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-2">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</div>
