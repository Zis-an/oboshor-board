@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h4>View Payroll</h4>
            <a href="{{route('payrolls.edit', $payroll->id)}}" class="btn btn-primary">Edit</a>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Work Days</th>
                    <th>Salary</th>
                    <th>status</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($payroll->items as $item)
                    <tr>
                        <td>{{$item->employee->name}}</td>
                        <td>{{$item->salary}}({{$item->salary_type}})</td>
                        <th>{{$item->status}}</th>
                        <td>{{$item->amount}}</td>
                    </tr>
                @endforeach
                <tr></tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
