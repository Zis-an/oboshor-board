@extends('layouts.app')

@section('main')

    @include('partials.error-alert', ['errors' => $errors])

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Add Cheque</h4>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => route('cheques.store'), 'files' => true]) !!}
            <div class="row">
                <div class="col-sm-6 mb-2">
                    {!! Form::label('account_id', 'Select Account', ['class' => 'control-label'])!!}
                    {!! Form::select('account_id', $accounts, $account->id ?? '', ['class' => 'form-control select2-search', 'placeholder' => 'Select Account']) !!}
                </div>
                <div class="col-sm-6 mb-2">
                    {!! Form::label('cheque_for_id', 'Cheque For', ['class' => 'control-label']) !!}
                    {!! Form::select('cheque_for_id', $providers, '', ['class' => 'form-control', 'placeholder' => 'Self']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label('number', 'Cheque Number', ['class' => 'control-label']) !!}
                    {!! Form::text('number','', ['class' => 'form-control', 'placeholder' => 'Cheque Number']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label('issue_date', 'Issue Date', ['class' => 'control-label']) !!}
                    {!! Form::text('issue_date','', ['class' => 'form-control date-time-picker', 'placeholder' => 'Issue Date']) !!}
                </div>

                <div class="col-sm-6 mb-2">
                    {!! Form::label('amount', 'Amount', ['class' => 'control-label']) !!}
                    {!! Form::text('amount','', ['class' => 'form-control', 'placeholder' => 'Amount']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('description mb-2', 'Description', ['class' => 'control-label']) !!}
                    {!! Form::text('description','', ['class' => 'form-control', 'placeholder' => 'Description']) !!}
                </div>

                {{-- Upload File --}}

                <div class="col-sm-6 mb-2">
                    {!! Form::label('file', 'Upload File', ['class' => 'control-label']) !!}
                    {!! Form::file('file', ['class' => 'form-control', 'placeholder' => 'Upload File']) !!}
                </div>

                <div class="col-12 mt-2">
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>

            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection
