<?php
$fiscalYears = [
    '2021-22' => '2021-22',
    '2022-23' => '2022-23',
    '2023-24' => '2023-24',
    '2024-25' => '2024-25',
    '2025-26' => '2025-26',
    '2026-27' => '2026-27',
    '2027-28' => '2027-28',
    '2028-29' => '2028-29',
    '2029-30' => '2029-30'
]
?>

@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Create Budget</h4>
        </div>
    </section>

    <section class="card">

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div>{{$error}}</div>
            @endforeach
        @endif


        {!! Form::open(['url' => route('budgets.update', $budget->id), 'method' => 'PUT']) !!}
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{Form::label('year', 'Fiscal Year')}}
                    {{Form::select('year', $fiscalYears, $budget->year, ['class' => 'form-control', 'placeholder' => 'Select Fiscal Year'])}}
                </div>
            </div>
        </div>
        <div class="card-body" id="budgetContainer">
            <div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($budgetItems as $budgetIndex=>$budgetItem)

                        <tr class="sub-total-row">

                            <input type="hidden"
                                   name="{{'items['. $budgetIndex . '][id]'}}"
                                   value="{{$budgetItem->id}}">
                            <td class="text-bold" style="width: 75%">{{$budgetItem->head->name}}</td>
                            <td style="width: 25%">
                                @if(count($budgetItem->items))
                                    {{Form::number('items['.$budgetIndex. '][amount]', $budgetItem->amount, ['class' => 'form-control sub-total', 'readonly' => true])}}
                                @else
                                    {{Form::number('items['.$budgetIndex. '][amount]', $budgetItem->amount, ['class' => 'form-control sub-total', ])}}
                                @endif
                            </td>
                        </tr>

                        @foreach($budgetItem->items as $index=>$item)
                            <tr>
                                <input type="hidden"
                                       name="{{'items['. $budgetIndex . '][child][' . $index.'][id]'}}"
                                       value="{{$item->id}}">
                                <td style="width: 75%">{{$item->head->name}}</td>
                                <td style="width: 25%">
                                    {{Form::number('items['.$budgetIndex. '][child]['  .$index.'][amount]', $item->amount, ['class' => 'form-control td-amount'])}}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>
                            <strong>Total</strong>
                        </td>
                        <td>
                            {!! Form::number('amount', $budget->amount, ['class' => 'form-control', 'id' => 'total-amount', 'readonly']) !!}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-2">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
        {!! Form::close() !!}



    </section>
@endsection

@push('scripts')
    <script>

        $(document).ready(function () {

            $(document).on('change', '.td-amount', function () {
                let amount = 0;

                let subTotalRow = $(this).closest('tr').prevAll(".sub-total-row:first");
                let subTotal = 0;
                $(subTotalRow).nextUntil('tr.sub-total-row').each(function (a, el) {
                    subTotal += Number(($(el).find('.td-amount')).val());
                    //let ll = $(el).find('.td-amount');
                    //console.log({ll});
                })

                $(subTotalRow).find('.sub-total').val(subTotal)

                //let dd = $(this).closest('.sub-total');

                //console.log({dd});

                $('#budgetContainer .sub-total').each((a, el) => {
                    amount += Number($(el).val());
                })
                $('#total-amount').val(amount)
            })

        })

    </script>
@endpush
