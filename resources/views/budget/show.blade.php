@extends('layouts.app')
@push('css')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        /* Set the width of the first column to 40% */
        th:first-child, td:first-child {
            width: 40%;
        }
        /* Divide the remaining 60% equally among the other three columns */
        th:not(:first-child), td:not(:first-child) {
            width: 20%;
        }
    </style>
@endpush
@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>View Budget ({{$type}}) {{$currentFinancialYear->name}}</h4>
        </div>
    </section>
    <section class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
                <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
            </div>
        </div>
        @php $totalAmount=0; @endphp
        <div class="card-body">
            <div>
                <table class="table table-bordered w-100">
                    <thead>
                    <tr>
                        <th style="width: 40%">Head Name</th>
                        @if(!empty($prevHeads))
                            <th style="width: 20%" class="text-capitalize">{{$type}}
                                Budget {{$prevFinancialYear->name}}</th>
                            @if($type == 'income')
                                <th>Actual Income {{$prevFinancialYear->name}}</th>
                            @endif
                            @if($type == 'expense')
                                <th style="width: 20%">Actual Expense {{$prevFinancialYear->name}}</th>
                            @endif
                            <th style="width: 20%" class="text-right text-capitalize">
                                {{$type}} Budget {{$currentFinancialYear->name}}
                            </th>
                            <th></th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($currentHeads as $headIndex=>$currentHead)
                        <!-- Newly Added -->
                        @if($currentHead->items->contains('status', 1))
                        <tr>
                            <td class="text-bold" style="width: 50%">{{$currentHead->name}}</td>
                            @if(!empty($prevHeads))
                                <td class="text-right font-weight-bold">{{number_format($prevHeads[$headIndex]->budget->amount ?? 0, 2)}}</td>
                                @if($type == 'income')
                                    @php
                                        $headAmount = $prevHeads[$headIndex]->transactions->sum('amount') ?? 0;
                                        $totalAmount += $headAmount;
                                    @endphp
                                    <td class="text-right font-weight-bold">{{number_format($headAmount, 2)}}</td>
                                @endif
                                @if($type == 'expense')
                                    @php
                                        $headAmount = $prevHeads[$headIndex]->budget->actual_amount ?? 0;
                                        $totalAmount += $headAmount;
                                    @endphp
                                    <td class="text-right font-weight-bold">{{number_format($headAmount, 2)}}</td>
                                @endif
                            @endif
                            <td class="text-right font-weight-bold">{{number_format($currentHead->budget->amount ?? 0, 2)}}</td>
                        </tr>
                        <!-- Newly Added -->
                        @endif
                        @foreach($currentHead->items as $index=>$item)
                            <!-- Newly Added -->
                            @if($item->status == 1)
                            <tr>
                                <td style="width: 75%">{{$item->name}}</td>
                                @if(!empty($prevHeads))
                                    <td class="text-right">{{number_format($prevHeads[$headIndex]['items'][$index]->budget->amount ?? 0, 2)}}</td>
                                    @if($type == 'income')
                                        <td class="text-right">{{number_format($prevHeads[$headIndex]['items'][$index]->transactions->sum('amount') ?? 0, 2)}}</td>
                                    @endif
                                    @if($type == 'expense')
                                        <td class="text-right">{{number_format($prevHeads[$headIndex]['items'][$index]->budget->actual_amount ?? 0, 2)}}</td>
                                    @endif
                                @endif
                                <td class="text-right" style="width: 25%">
                                    {{number_format($item->budget->amount ?? 0, 2)}}
                                </td>
                            </tr>
                            <!-- Newly Added -->
                            @endif
                        @endforeach
                    @endforeach
                    </tbody>
                    <tfoot style="background-color: #eee">
                    <tr class="bg-light">
                        <td>
                            <strong>Total</strong>
                        </td>
                        @if(!empty($prevFinancialYear))
                            <td class="text-right font-weight-bold">{{number_format($prevBudget->amount, 2)}}</td>
                            <td class="text-right font-weight-bold">{{number_format($totalAmount, 2)}}</td>
                        @endif
                        <td class="text-right font-weight-bold">
                            {{number_format($budget->amount, 2)}}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '#export_btn_pdf', function () {
                let url = window.location.pathname+`?export=true&type=pdf`
                window.open(url, '_blank');
            })
            $(document).on('click', '#export_btn_excel', function () {
                let url = window.location.pathname+`?export=true&type=excel`
                window.open(url, '_blank');
            })
        })
    </script>
@endpush
