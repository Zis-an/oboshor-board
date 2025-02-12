@extends('layouts.app')

@section('main')
    <div class="content-header">
        <div class="container-fluid">
            <h4>Settings</h4>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                {!! Form::open(['url' => route('settings.index')]) !!}
                <div class="row">

                    <div class="col-sm-4">
                        {!! Form::label('active_financial_year_id', 'Active Financial Year', ['class' => 'control-label']) !!}
                        {!! Form::select('active_financial_year_id', $financialYears, $setting->active_financial_year_id ?? '', ['class' => 'form-control', 'placeholder' => 'Active Financial Year']) !!}
                    </div>

                    <div class="col-sm-4">
                        {!! Form::label('approval_level', 'Approval Level', ['class' => 'control-label']) !!}
                        {!! Form::number('approval_level', $setting->approval_level ?? '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="col-sm-4">
                        {!! Form::label('required_level', 'Min. approval Level to Final', ['class' => 'control-label']) !!}
                        {!! Form::number('required_level', $setting->required_level ?? '', ['class' => 'form-control']) !!}
                    </div>

                </div>

                <div class="col-12 mt-2">
                    <button class="btn btn-primary float-right" type="submit">Save</button>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>

@endsection
