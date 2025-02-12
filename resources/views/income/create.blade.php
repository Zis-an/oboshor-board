@extends('layouts.app')

@section('main')

    <section class="content-header">
        <div class="container-fluid">

            <h4>Add Income</h4>

        </div>
    </section>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{$error}}</div>
        @endforeach
    @endif

    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => route('incomes.store'), 'id' => 'createIncomeForm', 'files' => true ]) !!}

            <div>
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="form-group">
                            {!! Form::label('date', 'Date*') !!}
                            {!! Form::date('date', now(), ['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-12 my-2">
                        <h5>Income Source</h5>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('head_id', 'Select Income Head*') !!}
                            {!! Form::select('head_id', $incomeHeads, '', ['class'=>'form-control', 'placeholder' => 'Select Income Head', 'id' => 'select_income_head']) !!}
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('head_item_id', 'Select Income Sub Head') !!}
                            {!! Form::select('head_item_id', [], '', ['class'=>'form-control', 'placeholder' => 'Select Income Head', 'id' => 'select_income_head_item']) !!}
                        </div>
                    </div>

                    <!-- Transaction Method -->

                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <div class="form-group">
                                {!! Form::label('method', 'Transaction Method*') !!}
                                {!! Form::select('method', $methods, '', ['class'=>'form-control', 'placeholder' => 'Select Income Head', 'id' => 'select_transaction_method']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none transaction-cheque">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('cheque_date', 'Cheque Date*') !!}
                                    {!! Form::date('cheque_date', '', ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('cheque_number', 'Cheque Number*') !!}
                                    {!! Form::text('cheque_number', '', ['class'=>'form-control', 'placeholder' => 'Cheque Number*']) !!}
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('bank', 'Bank') !!}
                                    {!! Form::text('bank', '', ['class'=>'form-control', 'placeholder' => 'Bank']) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('cheque_file', 'Upload Cheque Document/Image') !!}
                                    {!! Form::file('cheque_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 d-none transaction-beftn">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('bank', 'Bank*') !!}
                                    {!! Form::text('bank', '', ['class'=>'form-control', 'placeholder' => 'Bank*']) !!}
                                </div>
                            </div>
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

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('bank', 'Bank') !!}
                                    {!! Form::text('bank', '', ['class'=>'form-control', 'placeholder' => 'Bank']) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('pay_order_file', 'Upload Pay Order Document/Image') !!}
                                    {!! Form::file('pay_order_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                                </div>
                            </div>

                        </div>
                    </div>

                    {{--<div class="col-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('deposit_bank', 'Deposit To*') !!}
                            {!! Form::select('deposit_bank', $banks, '', ['class'=>'form-control', 'placeholder' => 'Select Bank', 'id' => 'selectIncomeBank']) !!}
                        </div>
                    </div>--}}

                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('account_id', 'Deposit Account*') !!}
                            {!! Form::select('account_id', $accounts , '', ['class'=>'form-control select2 select2-search', 'placeholder' => 'Select Account', 'id' => 'selectIncomeAccount']) !!}
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
                            {!! Form::label('amount', 'Amount*') !!}
                            {!! Form::text('amount', '', ['class'=>'form-control', 'placeholder' => 'Amount*']) !!}
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            {!! Form::label('description', 'Narration*') !!}
                            {!! Form::textarea('description', '', ['class'=>'form-control', 'rows'=> 2]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12 mt-3">
                        <button class="btn btn-primary float-right">Save</button>
                    </div>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function () {

            $(document).on('change', '#selectIncomeBank', function () {

                let bankId = this.value;

                $.ajax({
                    url: `/get-accounts-data?bank=${bankId}`,
                    success: function (data) {

                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let options = '<option>Select Account</option>';
                        data.map(item => {
                            options += `<option value='${item.id}'>${item.name}</option>`
                        })
                        $('#selectIncomeAccount').html(options);

                    },
                    error: function () {

                    }
                })

            })

            $(document).on('change', '#select_income_head', function () {

                let head = this.value;

                $.ajax({
                    url: `/get-head-items?head_id=${head}`,
                    success: function (data) {

                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let options = '<option>Select Item Head</option>';
                        data.map(item => {
                            options += `<option value='${item.id}'>${item.name}</option>`
                        })

                        $('#select_income_head_item').html(options);

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

                if (method === 'cheque') {
                    $('.transaction-cheque').removeClass('d-none')
                } else if (method === 'beftn') {
                    $('.transaction-beftn').removeClass('d-none')
                } else {
                    $('.transaction-pay-order').removeClass('d-none')
                }

            })

        })
    </script>
@endpush
