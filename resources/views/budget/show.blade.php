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












        .drag-handle {
            cursor: move;
            margin-right: 8px;
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
        @php
            $totalAmount=0;
        @endphp

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
{{--                    <tbody>--}}
{{--                    @foreach($currentHeads as $headIndex=>$currentHead)--}}
{{--                        @if($currentHead->items->contains('status', 1))--}}
{{--                        <tr>--}}
{{--                            <td class="text-bold" style="width: 50%">{{$currentHead->name}}</td>--}}
{{--                            @if(!empty($prevHeads))--}}
{{--                                <td class="text-right font-weight-bold">{{number_format($prevHeads[$headIndex]->budget->amount ?? 0, 2)}}</td>--}}
{{--                                @if($type == 'income')--}}
{{--                                    @php--}}
{{--                                        $headAmount = $prevHeads[$headIndex]->transactions->sum('amount') ?? 0;--}}
{{--                                        $totalAmount += $headAmount;--}}
{{--                                    @endphp--}}
{{--                                    <td class="text-right font-weight-bold">{{number_format($headAmount, 2)}}</td>--}}
{{--                                @endif--}}
{{--                                @if($type == 'expense')--}}
{{--                                    @php--}}
{{--                                        $headAmount = $prevHeads[$headIndex]->transactionItems->sum('amount') ?? 0;--}}
{{--                                        $totalAmount += $headAmount;--}}
{{--                                    @endphp--}}
{{--                                    <td class="text-right font-weight-bold">{{number_format($headAmount, 2)}}</td>--}}
{{--                                @endif--}}
{{--                            @endif--}}
{{--                            @php--}}
{{--                                $totalAmountOfHeadItems = $currentHead->items->sum(function ($item) {--}}
{{--                                    return $item->budget->amount ?? 0;--}}
{{--                                });--}}
{{--                            @endphp--}}
{{--                            <td class="text-right font-weight-bold">{{number_format($currentHead->budget->amount ?? 0, 2)}}</td>--}}
{{--                            <td class="text-right font-weight-bold">{{number_format($totalAmountOfHeadItems, 2)}}</td>--}}
{{--                            <!--<td></td>-->--}}
{{--                        </tr>--}}
{{--                        @endif--}}
{{--                        @foreach($currentHead->items as $index=>$item)--}}
{{--                            @if($item->status == 1)--}}

{{--                                <tr>--}}
{{--                                <td style="width: 75%">{{$item->name}}</td>--}}
{{--                                @if(!empty($prevHeads))--}}

{{--                                    <td class="text-right">{{number_format($prevHeads[$headIndex]['items'][$index]->budget->amount ?? 0, 2)}}</td>--}}

{{--                                    @if($type == 'income')--}}
{{--                                        <td class="text-right">{{number_format($prevHeads[$headIndex]['items'][$index]->transactions->sum('amount') ?? 0, 2)}}</td>--}}
{{--                                    @endif--}}

{{--                                    @if($type == 'expense')--}}
{{--                                        <td class="text-right">{{number_format($prevHeads[$headIndex]['items'][$index]->transactionItems->sum('amount') ?? 0, 2)}}</td>--}}
{{--                                    @endif--}}

{{--                                @endif--}}

{{--                                <td class="text-right" style="width: 25%">--}}
{{--                                    {{number_format($item->budget->amount ?? 0, 2)}}--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            @endif--}}
{{--                        @endforeach--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}



































                    @foreach($currentHeads as $headIndex=>$currentHead)
                        @if($currentHead->items->contains('status', 1))
                            <tbody class="head-group" data-head-id="{{ $currentHead->id }}">
                            <tr>
                                <td class="text-bold" style="width: 50%">
                                    <i class="fas fa-arrows-alt-v drag-handle" style="cursor: move; margin-right: 8px;"></i>
                                    {{ $currentHead->name }}
                                </td>
                                @if(!empty($prevHeads))
                                    @php
                                        // Get the previous head using ID lookup
                                        $prevHead = $prevHeads->get($currentHead->id);
                                    @endphp
                                    <td class="text-right font-weight-bold">{{ number_format($prevHead->budget->amount ?? 0, 2) }}</td>
                                    @if($type == 'income')
                                        @php
                                            $headAmount = $prevHead->transactions->sum('amount') ?? 0;
                                            $totalAmount += $headAmount;
                                        @endphp
                                        <td class="text-right font-weight-bold">{{ number_format($headAmount, 2) }}</td>
                                    @endif
                                    @if($type == 'expense')
                                        @php
                                            $headAmount = $prevHead->transactionItems->sum('amount') ?? 0;
                                            $totalAmount += $headAmount;
                                        @endphp
                                        <td class="text-right font-weight-bold">{{ number_format($headAmount, 2) }}</td>
                                    @endif
                                @endif
                                @php
                                    $totalAmountOfHeadItems = $currentHead->items->sum(function ($item) {
                                        return $item->budget->amount ?? 0;
                                    });
                                @endphp
                                <td class="text-right font-weight-bold">{{ number_format($totalAmountOfHeadItems, 2) }}</td>
                            </tr>
                            @foreach($currentHead->items as $index=>$item)
                                @if($item->status == 1)
                                    <tr>
                                        <td style="width: 75%">{{ $item->name }}</td>
                                        @if(!empty($prevHeads))
                                            @php
                                                $prevItem = $prevHead ? $prevHead->items->firstWhere('id', $item->id) : null;
                                            @endphp
                                            <td class="text-right">{{ number_format($prevItem->budget->amount ?? 0, 2) }}</td>
                                            @if($type == 'income')
                                                <td class="text-right">{{ number_format($prevItem->transactions->sum('amount') ?? 0, 2) }}</td>
                                            @endif
                                            @if($type == 'expense')
                                                <td class="text-right">{{ number_format($prevItem->transactionItems->sum('amount') ?? 0, 2) }}</td>
                                            @endif
                                        @endif
                                        <td class="text-right" style="width: 25%">
                                            {{ number_format($item->budget->amount ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                            @endif
                            @endforeach


































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























    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var table = document.querySelector('table');
            var headOrder = [];

            // Initialize headOrder with current order
            function updateHeadOrder() {
                headOrder = Array.from(document.querySelectorAll('tbody.head-group'))
                    .map(tbody => tbody.getAttribute('data-head-id'));
            }

            // Initialize Sortable
            Sortable.create(table, {
                handle: '.drag-handle',
                draggable: 'tbody.head-group',
                animation: 150,
                onEnd: function () {
                    updateHeadOrder();
                }
            });

            // Update export URLs with current order
            $(document).on('click', '#export_btn_pdf, #export_btn_excel', function (e) {
                e.preventDefault();
                const type = $(this).attr('id') === 'export_btn_pdf' ? 'pdf' : 'excel';
                const order = headOrder.join(',');
                let url = window.location.pathname + `?export=true&type=${type}&order=${order}`;
                window.open(url, '_blank');
            });

            // Initial order setup
            updateHeadOrder();
        });
    </script>















@endpush
