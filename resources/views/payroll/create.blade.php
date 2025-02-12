@extends('layouts.app')
@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Add Payroll</h4>
        </div>
    </section>

    <section class="card">
        <div class="card-body">

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div>{{$error}}</div>
                @endforeach
            @endif

            {!! Form::open(['url' => route('payrolls.store')]) !!}

            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', '', ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('date', 'Month') !!}
                {!! Form::text('date', '', ['class' => 'form-control', 'id' => 'payroll-month']) !!}
            </div>

            <div>
                <button class="btn btn-primary">Generate</button>
            </div>

            {!! Form::close() !!}
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            $('#payroll-month').datetimepicker({
                format: 'yyyy-MM-DD HH:mm:ss'
            });
        })
    </script>
@endpush
