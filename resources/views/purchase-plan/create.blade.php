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
            <h4 class="text-capitalize">Add Purchase Plan</h4>
        </div>
    </section>

    <section class="card">

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div>{{$error}}</div>
            @endforeach
        @endif

        {!! Form::open(['url' => route('purchase-plans.store')]) !!}
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{Form::label('year', 'Fiscal Year')}}
                    {{Form::select('year', $fiscalYears, '', ['class' => 'form-control', 'placeholder' => 'Select Fiscal Year'])}}
                </div>
            </div>
        </div>
        <input type="hidden" name="type" value="purchase"/>
        <div class="card-body" id="budgetContainer">
            <div>
                <table class="table table-bordered w-100">
                    <thead>
                    <tr>
                        <th style="width: 40%">Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($headItems as $headItemIndex=>$headItem)

                        <tr class="sub-total-row">
                            <input type="hidden"
                                   name="{{'items['. $headItemIndex . '][head_item_id]'}}"
                                   value="{{$headItem->id}}">
                            <td class="text-bold">{{$headItem->name}}</td>
                            <td></td>
                            <td></td>
                            <td style="width: 25%">
                                {{Form::number('items['.$headItemIndex. '][amount]', '', ['class' => 'form-control sub-total', 'readonly' => true])}}
                            </td>
                        </tr>
                        @if(isset($headItem->items))
                            @foreach( $headItem->items as $index=>$item)
                                <tr>
                                    <input type="hidden"
                                           name="{{'items['. $headItemIndex . '][child][' . $index.'][item_id]'}}"
                                           value="{{$item->id}}">
                                    <td>{{$item->name}}</td>
                                    <td style="width: 15%">
                                        {{Form::number('items['.$headItemIndex. '][child]['  .$index.'][quantity]', '', ['class' => 'form-control'])}}
                                    </td>
                                    <td style="width: 20%">
                                        {{Form::text('items['.$headItemIndex. '][child]['  .$index.'][unit]', '', ['class' => 'form-control'])}}
                                    </td>
                                    <td style="width: 20%">
                                        {{Form::number('items['.$headItemIndex. '][child]['  .$index.'][amount]', '', ['class' => 'form-control td-amount'])}}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                    </tbody>
                    <!--                        <tfoot>
                        <tr>
                            <td>
                                <strong>Total</strong>
                            </td>
                            <td>
                                {!! Form::number('amount', 0, ['class' => 'form-control', 'id' => 'total-amount', 'readonly']) !!}
                    </td>
                </tr>
                </tfoot>-->
                </table>
            </div>
            <table class="table w-100">
                <tr>
                    <th style="width:75%">Total Amount</th>
                    <td>
                        {!! Form::number('amount', 0, ['class' => 'form-control', 'id' => 'total-amount', 'readonly']) !!}
                    </td>
                </tr>
            </table>

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

            $(document).on('change', '.sub-total', function () {

                let amount = 0;

                $('#budgetContainer .sub-total').each((a, el) => {
                    amount += Number($(el).val());
                })

                $('#total-amount').val(amount)
            })

            $(document).on('change', '.td-amount', function () {

                let amount = 0;

                let subTotalRow = $(this).closest('tr').prevAll(".sub-total-row:first");
                let subTotal = 0;
                $(subTotalRow).nextUntil('tr.sub-total-row').each(function (a, el) {
                    subTotal += Number(($(el).find('.td-amount')).val());
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
