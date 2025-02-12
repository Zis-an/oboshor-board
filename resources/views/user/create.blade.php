@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Add User</h4>
        </div>
    </section>

    <section class="content">

        @if($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        @endif

        <div class="card">
            {!! Form::open(['url' => route('users.store')]) !!}
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                        {!! Form::text('name', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label('email', 'Email*', ['class' => 'control-label']) !!}
                        {!! Form::text('email', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label('user_id', 'User Id*', ['class' => 'control-label']) !!}
                        {!! Form::text('user_id', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label('password', 'Password*', ['class' => 'control-label']) !!}
                        {!! Form::text('password', '', ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::label('role', 'Select Role*', ['class' => 'control-label']) !!}
                        {!! Form::select('role', $roles, '', ['class' => 'form-control', 'placeholder' => 'Select Role']) !!}
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </section>

@endsection
