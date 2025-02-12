@extends('layouts.app')

@section('main')
    <section class="content-header">
        <h3>Add Leave Request</h3>
    </section>

    @include('partials.error-alert')

    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => route('leave-requests.store'), 'files' => true]) !!}
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('date', 'Date Range *') !!}
                        {!! Form::text('date', null, ['class' => 'form-control', 'required' => true, 'id' => 'date_range']) !!}
                    </div>
                </div>

                {{--<div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('employee_id', 'Employee *') !!}
                        {!! Form::select('employee_id', $employees, null, ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                </div>--}}

                <div class="col-sm-12">
                    {!! Form::label('reason', 'Reason') !!}
                    {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows' => 4]) !!}
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('file', 'File', ['class' => 'control-label']) !!}
                        {!! Form::file('file', ['class' => 'form-control']) !!}
                    </div>
                </div>
                {{--<div class="col-12">
                    <div class="alert alert-primary">
                        You are applying for <span id="leave_days">0</span> days leave
                    </div>
                </div>--}}
                <div class="col-12 mt-2">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#date_range').daterangepicker({
                locale: {
                    format: 'DD MMM YYYY',
                }
            })
        })
    </script>
@endpush
