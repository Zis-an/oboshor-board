@extends('layouts.app')

@section('main')

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Add Lot Item</h4>
            </div>
        </div>
    </section>

    @include('partials.error-alert', ['errors' => $errors])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['url' => route('add-lot-item.post', $lot->id)]) !!}
            <div class="row">

                <div class="col-sm-6">
                    {!! Form::label('applicant_serial_no', 'APP.SL.NO.') !!}
                    {!! Form::text('applicant_serial_no', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('date', 'Date') !!}
                    {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('receiver_name', 'Receiver Name') !!}
                    {!! Form::text('receiver_name', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('index', 'Index') !!}
                    {!! Form::text('index', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('city', 'DISTRICT/CITY') !!}
                    {!! Form::text('city', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('amount', 'Amount') !!}
                    {!! Form::text('amount', '', ['class' => 'form-control']) !!}
                </div>


                <div class="col-sm-6">
                    {!! Form::label('bank_name', 'Bank Name') !!}
                    {!! Form::text('bank_name', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('account_no', 'Bank Acc. No.') !!}
                    {!! Form::text('account_no', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('branch_name', 'Branch Name') !!}
                    {!! Form::text('branch_name', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-sm-6">
                    {!! Form::label('routing', 'Routing') !!}
                    {!! Form::text('routing', '', ['class' => 'form-control']) !!}
                </div>

                <div class="col-12 mt-2">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            datePicker();
        })
    </script>
@endpush
