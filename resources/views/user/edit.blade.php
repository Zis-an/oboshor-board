@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Update User</h4>
        </div>
    </section>

    <section class="content">

        @if($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        @endif

        <div class="card">
            {!! Form::open(['url' => route('users.update', [$user->id]), 'method' =>  'PUT']) !!}
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                            {!! Form::text('name', $user->name, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div>
                            {!! Form::label('email', 'Email*', ['class' => 'control-label']) !!}
                            {!! Form::text('email', $user->email, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div>
                            {!! Form::label('user_id', 'User Id*', ['class' => 'control-label']) !!}
                            {!! Form::text('user_id', $user->user_id, ['class' => 'form-control', 'readonly' => true]) !!}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div>
                            <div class="form-group">
                                {!! Form::label('role', 'Select Role*', ['class' => 'control-label']) !!}
                                {!! Form::select('role', $roles, $user->roles->first()->id , ['class' => 'form-control', 'placeholder' => 'Select Role']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('is_active', 'Status*', ['class' => 'control-label']) !!}
                            {!! Form::select('is_active', ['1' => 'Active', '0' => 'Inactive'] , $user->is_active , ['class' => 'form-control', 'placeholder' => 'Select Status']) !!}
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary float-right">Update</button>
                    </div>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </section>

@endsection
