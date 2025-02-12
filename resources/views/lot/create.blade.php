@extends('layouts.app')

@section('main')

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Add Lot</h4>
            </div>
        </div>
    </section>

    @include('partials.error-alert', ['errors' => $errors])

    <section class="card">

        <div class="card-body">

            {!! Form::open(['url' => '/lots', 'files' => true, 'enctype' => 'multipart/form-data']) !!}

            <div class="row">
                <div class='col-sm-6'>
                    <div class="form-group">
                        {!! Form::label('name', 'Lot Name', ['class' => 'control-label']) !!}
                        {!! Form::text('name', "", ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class='col-sm-6'>
                    <div class="form-group">
                        {!! Form::label('lot_number', 'Lot ID', ['class' => 'control-label']) !!}
                        {!! Form::text('lot_number', "", ['class' => 'form-control']) !!}
                    </div>
                </div>


                <div class='col-sm-6'>
                    <div class="form-group">
                        {!! Form::label('short_name', 'Lot Short Name', ['class' => 'control-label']) !!}
                        {!! Form::text('short_name', "", ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class='col-sm-6'>
                    <div class="form-group">
                        {!! Form::label('file_page', 'File Page', ['class' => 'control-label']) !!}
                        {!! Form::text('file_page', "", ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                        {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('account_id', 'Select Bank Account*', ['class' => 'control-label']) !!}
                        {!! Form::select('account_id', $accounts, '', ['class' => 'form-control select2 select2-search', 'placeholder' => 'Select Bank Account']) !!}
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('is_old', 'Is Old Lot', ['class' => 'control-label']) !!}
                        {!! Form::select('is_old', [0=> 'No', 1=> 'Yes'], 0, ['class' => 'form-control select2 select2-search', 'placeholder' => 'Select Bank Account']) !!}
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        {!! Form::label('file', 'Upload Excel File', ['class' => 'control-label']) !!}
                        {!! Form::file('file', ['class' => 'form-control', 'accept' =>'application/vnd.msexcel']) !!}
                    </div>
                </div>

                <div class="col-6">
                    <div class="form-group">
                        {!! Form::label('approval_file', 'Upload Approval Documents', ['class' => 'control-label']) !!}
                        {!! Form::file('approval_file', ['class' => 'form-control', 'accept' =>'application/pdf, image/png, image/jpeg, image/jpg']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>

            {!! Form::close() !!}

            <div class="mt-2">
                <h4>File Import Rules</h4>
                <hr/>
                <ul>
                    <li>1. Only Excel file is allowed</li>
                    <li>2. Header Content must be started in 6th Row</li>
                    <li>3. Main Record has to be started from 7th row</li>
                    <li>4. Please be aware last two row will be ignore.</li>
                </ul>
            </div>

        </div>

    </section>
@endsection
