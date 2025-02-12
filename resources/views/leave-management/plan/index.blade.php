@extends('layouts.app')

@section('main')
    <section class="content-header d-flex justify-content-between align-items-center">
        <h3>Leave Plans</h3>
        <a href="{{route('leave-plans.create')}}"
        class="btn btn-primary"
        >Add New</a>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="table">
                <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Number of Days</th>
                    <th>Taken</th>
                    <th>Days Remaining</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                ajax: window.location.pathname,
                columns: [
                    {data: 'employee_name'},
                    {data: 'balance'},
                    {data: 'taken'},
                    {data: 'remaining'},
                    {data: 'action'},
                ]
            })
        })
    </script>
@endpush
