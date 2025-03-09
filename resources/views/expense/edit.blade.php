@extends('layouts.app')
@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Edit Expense</h4>
        </div>
    </section>
    <section class="card">
        {!! Form::open(['url' => route('expenses.update', $expense->id), 'method' => 'PUT', 'files' => true]) !!}
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {{Form::label('date', 'Date')}}
                        {{Form::text('date', $expense->date, ['class' => 'form-control', 'placeholder' => 'Date', 'id' => 'expense-date'])}}
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {{Form::label('transaction_for', 'Select Service Provider*')}}
                        {{Form::select('transaction_for', $serviceProviders, $expense->transaction_for, ['class' => 'form-control', 'placeholder' => 'Service Provider'])}}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('file_no', 'File Number') !!}
                        {!! Form::text('file_no', $expense->file_no, ['class'=>'form-control', 'placeholder' => 'File Number*']) !!}
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <div class="form-group">
                            {{Form::label('method', 'Select Transaction Method')}}
                            {{Form::select('method', $methods, $expense->method, ['class' => 'form-control', 'placeholder' => 'Select Method', 'id' => 'select_transaction_method', 'disabled' => true])}}
                        </div>
                    </div>
                </div>
                <div class="col-12 transaction-cheque {{$expense->method == 'cheque' ? '': 'd-none' }}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheque_date', 'Cheque Date*') !!}
                                {!! Form::date('cheque_date', $expense->cheque_date, ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheque_number', 'Cheque Number*') !!}
                                {!! Form::text('cheque_number', $expense->cheque_number, ['class'=>'form-control', 'placeholder' => 'Cheque Number*']) !!}
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheque_file', 'Upload Cheque Document/Image') !!}
                                {!! Form::file('cheque_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                                @include('partials.file-list', ['files' => $expense->cheque_file])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 transaction-beftn {{$expense->method == 'beftn' ? '': 'd-none' }}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('beftn_transaction_id', 'Transaction Id') !!}
                                {!! Form::text('beftn_transaction_id', $expense->beftn_transaction_id, ['class'=>'form-control', 'placeholder' => 'Transaction Id']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 transaction-pay-order {{$expense->method == 'pay-order' ? '': 'd-none'}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheque_date', 'Pay Order Date*') !!}
                                {!! Form::date('cheque_date', $expense->pay_order_date, ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('pay_order_number', 'Pay Order Number*') !!}
                                {!! Form::text('pay_order_number', $expense->pay_order_number, ['class'=>'form-control', 'placeholder' => 'Pay Order Number*']) !!}
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                {!! Form::label('pay_order_file', 'Upload Pay Order Document/Image') !!}
                                {!! Form::file('pay_order_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                                @include('partials.file-list', ['files' => $expense->pay_order_file])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('account_id', 'Account*') !!}
                        {!! Form::text('account_id', $expense->account->name, ['class'=>'form-control', 'placeholder' => 'Select Account', 'id' => 'select_expense_account', 'readonly']) !!}
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('file', 'Upload Approved Document') !!}
                        {!! Form::file('file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Document']) !!}

                        @include('partials.file-list', ['files' => $expense->file])
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('tax_file', 'Upload Tax File') !!}
                        {!! Form::file('tax_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Document']) !!}
                        @include('partials.file-list', ['files' => $expense->tax_file])
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('vat_file', 'Upload Vat File') !!}
                        {!! Form::file('vat_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Document']) !!}
                        @include('partials.file-list', ['files' => $expense->vat_file])
                    </div>
                </div>
                <div class="col-12">
                    {{Form::label('description', 'Description')}}
                    {{Form::text('description', $expense->description, ['class' => 'form-control', 'placeholder' => 'Description'])}}
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="expense-table">
                <thead>
                <tr>
                    <th>Expense Head</th>
                    <th>Head Item</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($expense->expenseItems as $index=>$item)
                    <tr>
                        <input type="hidden" value="{{$index}}" name="index">
                        <td style="width: 30%">
                            {{Form::select('items['.$index.'][head_id]', $expenseHeads, $item->head_id, ['class' => 'form-control', 'placeholder' => 'Select Expense Head'])}}
                        </td>
                        <td style="width: 35%">
                            {{Form::select('items['. $index .'][head_item_id]', $item->dependentSubHeads->pluck('name', 'id'), $item->head_item_id, ['class' => 'form-control select-expense-head-item', 'placeholder' => 'Select Expense Head Item'])}}
                        </td>
                        <td style="width: 15%">
                            {{Form::number('items['.$index.'][amount]', $item->amount, ['class' => 'form-control td-amount'])}}
                        </td>
                        <td>
                            <button class="remove-item-btn btn-sm btn-danger" type="button">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td><strong>Total</strong></td>
                    <td>{!! Form::number('amount', $expense->amount, ['class' => 'form-control', 'id' => 'sub_total', 'readonly']) !!}</td>
                    <td><button class="add-more-btn btn-sm btn-info" type="button"><i class="fa fa-plus"></i></button></td>
                </tr>
                </tfoot>
            </table>
            <table class="w-100 table-borderless mt-3">
                <tr>
                    <td style="width: 40%"></td>
                    <td>VAT</td>
                    <td><input type="text" class="form-control" placeholder="Percent" id="vat_percent" value="{{$vatPercent}}"></td>
                    <td><input name="vat" class="form-control" placeholder="amount" id="vat_amount" value="{{$expense->vat}}" readonly/></td>
                </tr>
                <tr>
                    <td style="width: 40%"></td>
                    <td>TAX</td>
                    <td style="width: 20%"><input type="text" class="form-control" placeholder="Percent" id="tax_percent" value="{{$taxPercent}}"></td>
                    <td><input name="tax" class="form-control" placeholder="amount" id="tax_amount" value="{{$expense->tax}}" readonly/></td>
                </tr>
                <tr>
                    <td style="width: 40%"></td>
                    <td>Grand Total</td>
                    <td style="width: 20%"></td>
                    <td><input name="amount_after_tax" class="form-control" placeholder="amount" id="total_amount" value="{{$amountAfterTax}}"/></td>
                </tr>
            </table>
            <!-- Submit Button -->
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
            $('#expense-datepicker').datetimepicker({
                format: 'yyyy-MM-DD HH:mm:ss'
            });
            $(document).on('change', '.td-amount', function () {
                calculateTotal();
            })

            $(document).on("click", ".add-more-btn", function () {
                let index = $("#expense-table tbody tr").length; // Get the next available index
                let newRow = `<tr>
                        <input type="hidden" value="${index}" name="index">
                        <td style="width: 35%">
                            <select class="form-control select-expense-head" name="items[${index}][head_id]">
                                <option value="">Select Expense Head</option>
                                @foreach($expenseHeads as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                </select>
            </td>
            <td style="width: 35%">
                <select class="form-control select-expense-head-item" name="items[${index}][head_item_id]">
                                <option value="">Select Item Head</option>
                            </select>
                        </td>
                        <td style="width: 20%">
                            <input type="text" class="form-control td-amount" name="items[${index}][amount]" value="">
                        </td>
                        <td>
                            <button class="remove-item-btn btn-sm btn-danger" type="button">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                $("#expense-table tbody").append(newRow);
            });

            $(document).on("click", ".remove-item-btn", function () {
                $(this).closest("tr").remove();
                $("#expense-table tbody tr").each(function (i) {
                    $(this).find("input, select").each(function () {
                        let name = $(this).attr("name");
                        if (name) {
                            name = name.replace(/\[\d+\]/, `[${i}]`);
                            $(this).attr("name", name);
                        }
                    });
                });
                calculateTotal();
            });
            //on select expense head
            $(document).on('change', '.select-expense-head', function () {
                let head = this.value;
                let el = $(this);
                $.ajax({
                    url: `/get-head-items?head_id=${head}`,
                    success: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let options = '<option>Select Item Head</option>';
                        data.map(item => {
                            options += `<option value='${item.id}'>${item.name}</option>`
                        })
                        $(el).closest('tr').find('.select-expense-head-item').html(options);
                    },
                    error: function () {
                    }
                })
            })

            function calculateTotal() {
                let amount = 0;
                $('#expense-table tbody>tr').each((a, el) => {
                    amount += Number($(el).find('.td-amount').val());
                })
                $('#sub_total').val(amount)
                calculateVatAndAmount();
            }
            $(document).on('change', '#vat_percent, #tax_percent', function () {
                calculateVatAndAmount()
            })
            function calculateVatAndAmount() {
                let subTotal = parseFloat($('#sub_total').val())
                let percentVat = $('#vat_percent').val() || 0;
                let amountVat = (subTotal * parseFloat(percentVat)) / 100;
                $('#vat_amount').val(amountVat)
                let percentTax = $('#tax_percent').val() || 0;
                let amountTax = (subTotal * parseFloat(percentTax)) / 100;
                $('#tax_amount').val(amountTax)
                let totalAmount = subTotal - (amountVat + amountTax)
                $('#total_amount').val(totalAmount)
            }
        })
    </script>
    <script>
        document.addEventListener("keydown", function(event) {
            if (event.key === "Enter" && event.target.tagName !== "TEXTAREA") {
                event.preventDefault();
            }
        });
    </script>
@endpush
