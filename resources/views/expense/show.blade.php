@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h4>View Expense</h4>
            <a href="{{route('expenses.edit', $expense->id)}}" class="btn btn-primary">Edit</a>
        </div>
    </section>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th>Date</th>
                    <td>{{\Carbon\Carbon::parse($expense->date)->format('d-m-Y-H-i A')}}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{number_format($expense->amount, 2)}}</td>
                </tr>
                <tr>
                    <th>Tax</th>
                    <td>{{number_format($expense->tax, 2)}}</td>
                </tr>
                <tr>
                    <th>VAT</th>
                    <td>{{number_format($expense->vat, 2)}}</td>
                </tr>
                <tr>
                    <th>Payable Amount</th>
                    <td>{{number_format($expense->amount - ($expense->vat + $expense->tax), 2)}}</td>
                </tr>

                <tr>
                    <th>Narration</th>
                    <td>{{$expense->description}}</td>
                </tr>

                <tr>
                    <th>Files</th>
                    <td></td>
                </tr>

                <tr>
                    <th>Approval File</th>
                    <td>
                        @include('partials.file-list',['files' => $expense->file])
                    </td>
                </tr>

                <tr>
                    <th>Tax File</th>
                    <td>
                        @include('partials.file-list',['files' => $expense->tax_file])
                    </td>
                </tr>

                <tr>
                    <th>VAT File</th>
                    <td>
                        @include('partials.file-list',['files' => $expense->vat_file])
                    </td>
                </tr>

                @if($expense->method == 'cheque')
                    <tr>
                        <th>Cheque File</th>
                        <td>
                            @include('partials.file-list',['files' => $expense->cheque_file])
                        </td>
                    </tr>
                @endif

                @if($expense->method == 'pay-order')
                    <tr>
                        <th>Pay Order File</th>
                        <td>@include('partials.file-list', ['files' => $expense->pay_order_file])</td>
                    </tr>
                @endif

            </table>

            <table class="table mt-3">
                <thead>
                <tr>
                    <th>Head</th>
                    <th>Sub Head</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($expense->expenseItems as $item)
                    <tr>
                        <td>{{$item->head->name ?? ''}}</td>
                        <td>{{$item->headItem->name ?? ''}}</td>
                        <td>{{number_format($item->amount, 2)}}</td>
                    </tr>
                @endforeach
                <tr></tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
