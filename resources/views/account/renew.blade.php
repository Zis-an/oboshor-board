@extends('layouts.app')

@section("main")
    <div>
        <div class="card">
            <div class="card-body">

                {!! Form::open(['url' => route('accounts.post-renew', $account->id), 'files' => true]) !!}

                <div class="row">

                    <div class="col-12">
                        <h5>Renewal Information</h5>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("start_date", 'Start Date *', ['class' => 'control-label']) !!}
                            {!! Form::date('start_date', '', ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("date", 'Transaction Date', ['class' => 'control-label']) !!}
                            {!! Form::datetimelocal('date', now(), ['class' => 'form-control']) !!}
                        </div>
                    </div>


                    {{--
                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("profit", 'End Date', ['class' => 'control-label']) !!}
                            {!! Form::date('profit', $profit, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    --}}

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("maturity_period", 'Maturity Period(Months) *', ['class' => 'control-label']) !!}
                            {!! Form::text('maturity_period', $account->maturity_period, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("interest_rate", 'Interest Rate *', ['class' => 'control-label']) !!}
                            {!! Form::text('interest_rate', $account->interest_rate, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-12">
                        <h5>Profit Information</h5>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("profit", 'Profit', ['class' => 'control-label']) !!}
                            {!! Form::text('profit', $profit, ['class' => 'form-control', 'id' => 'profit_amount']) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("profit_file", 'Profit File', ['class' => 'control-label']) !!}
                            {!! Form::file('profit_file', ['class' => 'form-control', 'accept' => 'image/*, application/pdf']) !!}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("tax_percent", 'Tax Percent', ['class' => 'control-label']) !!}
                            {!! Form::text('tax_percent', '', ['class' => 'form-control', 'id' => 'tax_percent']) !!}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("tax_amount", 'Tax Amount', ['class' => 'control-label']) !!}
                            {!! Form::text('tax_amount', '', ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("tax_file", 'Tax File', ['class' => 'control-label']) !!}
                            {!! Form::file('tax_file', ['class' => 'form-control', 'accept' => 'image/*, application/pdf']) !!}
                        </div>
                    </div>


                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("excise_amount", 'Excise Amount', ['class' => 'control-label']) !!}
                            {!! Form::text('excise_amount', 0, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("excise_date", 'Excise Date', ['class' => 'control-label']) !!}
                            {!! Form::datetimelocal('excise_date', '', ['class' => 'form-control','placeholder' => 'Excise Date']) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("excise_file", 'Excise File', ['class' => 'control-label']) !!}
                            {!! Form::file('excise_file', ['class' => 'form-control', 'accept' => 'image/*, application/pdf']) !!}
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('profit_after_tax', 'Amount After Tax', ['class' => 'control-label']) !!}
                            {!! Form::text('profit_after_tax', '', ['class' => 'form-control', 'id' => 'profit_after_tax', 'readonly' => true]) !!}
                        </div>
                        <!--                        <input type="text" name="profit_after_tax" id="profit_after_tax"
                                                       class="form-control" readonly
                                                >-->
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("Transfer Percent", 'Transfer Percent *', ['class' => 'control-label']) !!}
                            {!! Form::text('transfer_percent', '', ['class' => 'form-control', 'id' => 'transfer_percent']) !!}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("transfer_amount", 'Transfer  Amount', ['class' => 'control-label']) !!}
                            {!! Form::text('transfer_amount', '', ['class' => 'form-control', 'id' => 'transfer_amount']) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("transfer_file", 'Transfer File', ['class' => 'control-label']) !!}
                            {!! Form::file('transfer_file', ['class' => 'form-control', 'accept' => 'image/*, application/pdf']) !!}
                        </div>
                    </div>

                    <div class="col-sm-6 mb-2">
                        {!! Form::label("head_id", 'Income Head *') !!}
                        {!! Form::select("head_id", $incomeHeads, '', ['class' => 'form-control','placeholder' => 'Select Income Head', 'required' => 'required']) !!}
                    </div>

                    <div class="col-sm-6 mb-2">
                        {!! Form::label("head_item_id", 'Income Sub Head') !!}
                        {!! Form::select("head_item_id", [], '', ['class' => 'form-control','placeholder' => 'Select Income Head']) !!}
                    </div>

                    <div class="col-12">
                        <h5>Transfer To</h5>
                    </div>

                    {{--<div class="col-4">
                        <div class="form-group">
                            {!! Form::label("bank", 'Select Bank', ['class' => 'control-label']) !!}
                            {!! Form::select('bank', $banks,  '', ['class' => 'form-control', 'id' => 'select_bank', 'placeholder' => 'Select Bank']) !!}
                        </div>
                    </div>--}}

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label('account_id', 'Select Account *', ['class' => 'control-label']) !!}
                            {!! Form::select('account_id', $accounts,  '', ['class' => 'form-control select2-search', 'id' => 'select_account', 'placeholder' => 'Select Account']) !!}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            {!! Form::label("transfer_date", 'Transfer Date*', ['class' => 'control-label']) !!}
                            {!! Form::datetimelocal('transfer_date', '', ['class' => 'form-control','placeholder' => 'Profit Transfer Date']) !!}
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary">Save</button>
                        <button class="btn btn-warning" type="button" id="save_and_close">Save and Close</button>
                    </div>

                </div>

                {!! Form::close() !!}
            </div>

        </div>

        @endsection

        @push('scripts')
            <script>
                $(document).on('change', '#select_bank', function () {

                    let bankId = this.value;

                    $.ajax({
                        url: `/get-accounts-data?bank=${bankId}`,
                        success: function (data) {

                            // Transforms the top-level key of the response object from 'items' to 'results'
                            let options = '<option>Select Account</option>';
                            data.map(item => {
                                options += `<option value='${item.id}'>${item.name}</option>`
                            })
                            $('#select_account').html(options);

                        },
                        error: function () {

                        }
                    })

                })

                $(document).on('change', '#tax_percent', function () {
                    let percent = this.value;
                    let profit = parseFloat($('#profit_amount').val());
                    let taxAmount = (parseFloat(percent) * (isNaN(profit) ? 0 : profit)) / 100;
                    $('#tax_amount').val(taxAmount)
                    profitAfterTax();
                });

                $(document).on('change', '#excise_amount', function () {
                    profitAfterTax();
                })

                $(document).on('change', '#transfer_percent', function () {
                    let percent = this.value;
                    let profitAfterTax = parseFloat($('#profit_after_tax').val());
                    let transferAmount = (parseFloat(percent) * (isNaN(profitAfterTax) ? 0 : profitAfterTax)) / 100;
                    $('#transfer_amount').val(transferAmount)
                });

                $(document).on('click', '#save_and_close', function () {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, close it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let form = $(this).closest('form');
                            form.append("<input type='hidden' name='close' value='true'/>");
                            form.submit();
                        }
                    })

                })

                function profitAfterTax() {

                    let tax = parseOrReturnZeroIfNull($('#tax_amount').val());
                    let exciseAmount = parseOrReturnZeroIfNull($('#excise_amount').val());
                    console.log({tax, exciseAmount})
                    let restAmount = parseOrReturnZeroIfNull($('#profit_amount').val()) - (tax + exciseAmount);
                    $('#profit_after_tax').val(restAmount);
                }

                $(document).on('change', '#head_id', function () {

                    let head = this.value;

                    $.ajax({
                        url: `/get-head-items?head_id=${head}`,
                        success: function (data) {

                            // Transforms the top-level key of the response object from 'items' to 'results'
                            let options = '<option value="">Select Item Head</option>';
                            data.map(item => {
                                options += `<option value='${item.id}'>${item.name}</option>`
                            })

                            $('#head_item_id').html(options);

                        },
                        error: function () {

                        }
                    })

                })

            </script>
    @endpush
