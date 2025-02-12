@extends('layouts.app')

@section('main')

    <section class="content-header">
        <div class="container-fluid">
            <h4>Payroll List</h4>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="table" id="payroll-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Generate Date</th>
                    <th>Approved On</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function () {
            $('#payroll-table').dataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '/payrolls',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'name'},
                    {data: 'date'},
                    {data: 'approved_on'},
                    {data: 'created_by'},
                    {data: 'actions'},
                ],
            });
        })
    </script>
@endpush
