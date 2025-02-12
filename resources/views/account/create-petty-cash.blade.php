@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Create Petty Cash Account</h4>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            {!! Form::open(['url' => 'post-petty-cash-account']) !!}
            <div class="row">
                <div class="col-sm-12">
                    {!! Form::label('name', 'Petty Cash', ['class' => 'control-label']) !!}
                    {!! Form::text('name', 'Petty Cash', ['class' => 'form-control', 'disabled' => true, 'placeholder' => 'Petty Cash']) !!}
                </div>
                <div class="col-sm-12">
                    {!! Form::label('balance', 'Opening Balance', ['class' => 'control-label']) !!}
                    {!! Form::text('balance',  '', ['class' => 'form-control', 'placeholder' => 'Opening Balance']) !!}
                </div>

                <div class="col-sm-12 mt-3">
                    <button class="btn btn-primary">Submit</button>
                </div>

            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection
