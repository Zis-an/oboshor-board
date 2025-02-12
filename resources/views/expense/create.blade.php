@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Add Expense</h4>
        </div>
    </section>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{$error}}</div>
        @endforeach
    @endif

    <section class="card">
        {!! Form::open(['url' => route('expenses.store'), 'files' => true]) !!}
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {{Form::label('date', 'Date')}}
                        {{Form::text('date', now(), ['class' => 'form-control', 'placeholder' => 'Date', 'id' => 'expense-date'])}}
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {{Form::label('transaction_for', 'Select Service Provider*')}}
                        {{Form::select('transaction_for', $serviceProviders, '', ['class' => 'form-control', 'placeholder' => 'Service Provider'])}}
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('file_no', 'File Number') !!}
                        {!! Form::text('file_no', '', ['class'=>'form-control', 'placeholder' => 'File Number*']) !!}
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <div class="form-group">
                            {{Form::label('method', 'Select Transaction Method')}}
                            {{Form::select('method', $methods, '', ['class' => 'form-control', 'placeholder' => 'Select Method', 'id' => 'select_transaction_method'])}}
                        </div>
                    </div>
                </div>

                <div class="col-12 d-none transaction-cheque">

                    <div class="row">

                        <h5 class="col-12">Cheque For Party</h5>

                        <input type="hidden" name="cheques[0][type]"
                               value="party" />

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[0][cheque_date]', 'Cheque Date*') !!}
                                {!! Form::date('cheques[0][cheque_date]', '', ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[0][cheque_number]', 'Cheque Number*') !!}
                                {!! Form::text('cheques[0][cheque_number]', '', ['class'=>'form-control', 'placeholder' => 'Cheque Number*']) !!}
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[0][cheque_file]', 'Upload Cheque Document/Image') !!}
                                {!! Form::file('cheques[0][cheque_file]', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                            </div>
                        </div>

                    </div>

                    <div class="row">


                        <input type="hidden" name="cheques[1][type]"
                               value="vat" />

                        <h5 class="col-12">Cheque For VAT</h5>

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[1][cheque_date]', 'Cheque Date*') !!}
                                {!! Form::date('cheques[1][cheque_date]', '', ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[1][cheque_number]', 'Cheque Number*') !!}
                                {!! Form::text('cheques[1][cheque_number]', '', ['class'=>'form-control', 'placeholder' => 'Cheque Number*']) !!}
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[1][cheque_file]', 'Upload Cheque Document/Image') !!}
                                {!! Form::file('cheques[1][cheque_file]', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                            </div>
                        </div>


                    </div>

                    <div class="row">

                        <h5 class="col-12">Cheque For Tax</h5>


                        <input type="hidden" name="cheques[2][type]"
                               value="tax" />


                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[2][cheque_date]', 'Cheque Date*') !!}
                                {!! Form::date('cheques[2][cheque_date]', '', ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[2][cheque_number]', 'Cheque Number*') !!}
                                {!! Form::text('cheques[2][cheque_number]', '', ['class'=>'form-control', 'placeholder' => 'Cheque Number*']) !!}
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheques[2][cheque_file]', 'Upload Cheque Document/Image') !!}
                                {!! Form::file('cheques[2][cheque_file]', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                            </div>
                        </div>


                    </div>

                </div>

                <div class="col-12 d-none transaction-beftn">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('beftn_transaction_id', 'Transaction Id') !!}
                                {!! Form::text('beftn_transaction_id', '', ['class'=>'form-control', 'placeholder' => 'Transaction Id']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-none transaction-pay-order">
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('cheque_date', 'Pay Order Date*') !!}
                                {!! Form::date('cheque_date', '', ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('pay_order_number', 'Pay Order Number*') !!}
                                {!! Form::text('pay_order_number', '', ['class'=>'form-control', 'placeholder' => 'Pay Order Number*']) !!}
                            </div>
                        </div>

                        {{--                        <div class="col-sm-6">
                                                    <div class="form-group">
                                                        {!! Form::label('bank', 'Bank') !!}
                                                        {!! Form::text('bank', '', ['class'=>'form-control', 'placeholder' => 'Bank']) !!}
                                                    </div>
                                                </div>--}}

                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                {!! Form::label('pay_order_file', 'Upload Pay Order Document/Image') !!}
                                {!! Form::file('pay_order_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                            </div>
                        </div>

                    </div>
                </div>


                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('account_id', 'Account*') !!}
                        {!! Form::select('account_id', $accounts, '', ['class'=>'form-control', 'placeholder' => 'Select Account', 'id' => 'select_expense_account']) !!}
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('file', 'Upload Approved Document') !!}
                        {!! Form::file('file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Document']) !!}
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('tax_file', 'Upload Tax File') !!}
                        {!! Form::file('tax_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Document']) !!}
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('vat_file', 'Upload Vat File') !!}
                        {!! Form::file('vat_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Document']) !!}
                    </div>
                </div>

                <div class="col-12">
                    {{Form::label('description', 'Description')}}
                    {{Form::text('description', '', ['class' => 'form-control', 'placeholder' => 'Description', 'rows' => 2])}}
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="expense-table">
                <thead>
                <tr>
                    <th>Expense Head</th>
                    <th>Item Head</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <input type="hidden" value="0" name="index">
                    <td style="width: 35%">
                        {{Form::select('items[0][head_id]', $expenseHeads, '', ['class' => 'form-control select-expense-head', 'placeholder' => 'Select Expense Head'])}}
                    </td>
                    <td style="width: 35%">
                        {{Form::select('items[0][head_item_id]', [], '', ['class' => 'form-control select-expense-head-item', 'placeholder' => 'Select Expense Head Item'])}}
                    </td>
                    <td style="width: 20%">
                        {{Form::text('items[0][amount]', '', ['class' => 'form-control td-amount'])}}
                    </td>
                    <td>
                        <button class="remove-item-btn btn-sm btn-danger" type="button">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td><strong>Total</strong></td>
                    <td>
                        {!! Form::text('amount', 0, ['class' => 'form-control', 'id' => 'sub_total', 'readonly']) !!}
                    </td>
                    <td>
                        <button class="add-more-btn btn-sm btn-info" type="button">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
                </tfoot>
            </table>

            <table class="w-100 table-borderless mt-3">
                <tr>
                    <td style="width: 40%"></td>
                    <td>VAT</td>
                    <td>
                        <input type="text" class="form-control" placeholder="Percent" id="vat_percent">
                    </td>
                    <td>
                        <input name="vat" class="form-control" placeholder="amount" id="vat_amount" readonly/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 40%"></td>
                    <td>TAX</td>
                    <td style="width: 20%">
                        <input type="text" class="form-control" placeholder="Percent" id="tax_percent">
                    </td>
                    <td>
                        <input name="tax" class="form-control" placeholder="amount" id="tax_amount" readonly/>
                    </td>
                </tr>

                <tr>
                    <td style="width: 40%"></td>
                    <td>Grand Total</td>
                    <td style="width: 20%"></td>
                    <td>
                        <input name="amount_after_tax" class="form-control" placeholder="amount" id="total_amount"/>
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

            $('#select_expense_account').select2({
                autoWidth: false,
            });

            $('#expense-date').datetimepicker({
                format: 'yyyy-MM-DD hh:mm:ss',
                ignoreReadonly: true,
            });

            $(document).on('change', '.td-amount', function () {
                calculateTotal();
            })

            $(document).on('click', '.add-more-btn', function () {
                let lll = $('#expense-table tbody>tr:last')
                let index = Number($(lll).find('input[name=index]').val()) + 1;
                let prefix = "items[" + index + "]";
                let cloned = $(lll).clone().find('input, select')
                    .each(function (ind, el) {
                        this.name = this.name.replace(/items\[\d+]/, prefix);
                        this.value = '';
                    }).end();

                $('#expense-table').append(cloned)
            })

            $(document).on('click', '.remove-item-btn', function () {
                $(this).closest('tr').remove();
                calculateTotal();
            })

            function calculateTotal() {
                let amount = 0;
                $('#expense-table tbody>tr').each((a, el) => {
                    amount += Number($(el).find('.td-amount').val());
                })
                $('#sub_total').val(amount)
                calculateVatAndAmount()
            }

            //on select bank

            $(document).on('change', '#select_expense_bank', function () {

                let bankId = this.value;

                $.ajax({
                    url: `/get-accounts-data?bank=${bankId}`,
                    success: function (data) {

                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let options = '<option>Select Account</option>';
                        data.map(item => {
                            options += `<option value='${item.id}'>${item.name}</option>`
                        })
                        $('#select_expense_account').html(options);

                    },
                    error: function () {

                    }
                })
            })

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

            $(document).on('change', '#select_transaction_method', function () {

                let method = this.value;

                $('.transaction-beftn').addClass('d-none');

                $('.transaction-pay-order').addClass('d-none');

                $('.transaction-cheque').addClass('d-none');

                $('#select_expense_bank').removeAttr('disabled')

                $('#select_expense_account').removeAttr('disabled');

                if (method === 'cheque') {
                    $('.transaction-cheque').removeClass('d-none')
                } else if (method === 'beftn') {
                    $('.transaction-beftn').removeClass('d-none')
                } else if (method === 'pay-order') {
                    $('.transaction-pay-order').removeClass('d-none')
                } else if (method === 'cash') {
                    $('#select_expense_bank').attr('disabled', true)
                    $('#select_expense_account').attr('disabled', true);
                }

            })

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
@endpush
