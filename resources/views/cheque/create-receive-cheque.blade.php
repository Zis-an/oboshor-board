@extends('layouts.app')

@section('main')

    @include('partials.error-alert', ['errors' => $errors])

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Add Receive Cheque</h4>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => route('cheques.store'), 'files' => true]) !!}
            <input type="hidden" name="type" value="received"/>
            <div class="row">
                {{--<div class="col-sm-6 mb-2">
                    {!! Form::label('account_id', 'Select Account', ['class' => 'control-label'])!!}
                    {!! Form::select('account_id', $accounts, $account->id ?? '', ['class' => 'form-control select2-search', 'placeholder' => 'Select Account']) !!}
                </div>
                <div class="col-sm-6 mb-2">
                    {!! Form::label('cheque_for_id', 'Cheque For', ['class' => 'control-label']) !!}
                    {!! Form::select('cheque_for_id', $providers, '', ['class' => 'form-control', 'placeholder' => 'Self']) !!}
                </div>--}}

                <div class="col-sm-6 mb-2">
                    {!! Form::label('number', 'Cheque Number', ['class' => 'control-label']) !!}
                    {!! Form::text('number','', ['class' => 'form-control', 'placeholder' => 'Cheque Number']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label('receive_date', 'Receive Date *', ['class' => 'control-label']) !!}
                    {!! Form::text('receive_date','', ['class' => 'form-control date-time-picker', 'placeholder' => 'Receive Date']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label('issue_date', 'Cheque Date *', ['class' => 'control-label']) !!}
                    {!! Form::text('issue_date','', ['class' => 'form-control date-time-picker', 'placeholder' => 'Cheque Date']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label("head_id", 'Head', ['class' => 'control-label']) !!}
                    {!! Form::select('head_id', $heads, '', ['class' => 'form-control', 'placeholder' => 'Select Head']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label("head_item_id", 'Head Item', ['class' => 'control-label']) !!}
                    {!! Form::select('head_item_id', [], '', ['class' => 'form-control', 'placeholder' => 'Select Head Item']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label('received_from_id', 'Cheque Received From', ['class' => 'control-label']) !!}
                    {!! Form::select('received_from_id', $providers, '', ['class' => 'form-control', 'placeholder' => 'Select']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label('amount', 'Amount', ['class' => 'control-label']) !!}
                    {!! Form::text('amount','', ['class' => 'form-control', 'placeholder' => 'Amount']) !!}
                </div>

                <div class="col-sm-4 mb-2">
                    {!! Form::label('file', 'Document') !!}
                    {!! Form::file('file', ['class' => 'form-control', 'accept' => 'image/*, application/pdf']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('description mb-2', 'Description', ['class' => 'control-label']) !!}
                    {!! Form::text('description','', ['class' => 'form-control', 'placeholder' => 'Description']) !!}
                </div>

                <div class="col-12 mt-2">
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>

            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        $(document).ready(function () {

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

        })

    </script>
@endpush
