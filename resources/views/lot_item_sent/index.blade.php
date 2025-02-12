@extends('layouts.app')

@section('main')

<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <h4>Add Index to send</h4>
        </div>
    </div>
</section>

@include('partials.error-alert', ['errors' => $errors])

<section class="card">

    <div class="card-body">

        {!! Form::open(['url' => '/old-lot-transection-post', 'files' => true, 'enctype' => 'multipart/form-data']) !!}

        <div class="row">               

            <div class="col-6">
                <div class="form-group">
                    {!! Form::label('file', 'Upload Excel File', ['class' => 'control-label']) !!}
                    {!! Form::file('file', ['class' => 'form-control', 'accept' =>'application/vnd.msexcel']) !!}
                </div>
                <div class="form-group">
                    <label for="validationCustom04" class="form-label">Bank Name</label><br />
                    <select name="bank_name" class="form-select" id="validationCustom04" required>
                        <option selected disabled value="">Select Bank</option>
                        <option value="1">Agrani Bank</option>
                        <option value="2">First Security Islami Bank</option>
                        <option value="3">Citizens Bank</option>
                    </select>
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
            </ul>
        </div>

    </div>



</section>
@endsection
