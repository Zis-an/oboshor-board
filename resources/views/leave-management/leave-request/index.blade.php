@extends('layouts.app')

@section('main')
    <div class="content-header d-flex justify-content-between align-items-center">
        <h3>Leave Requests</h3>
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">Add Leave Request</a>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="table">
                <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>From Date</th>
                    <th>End Date</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </table>
        </div>
    </div>

    <x-modal-fade id="view_modal"/>

@endsection

@push('scripts')
    <script>

        let table = '';

        $(document).ready(function () {
            table = $('#table').DataTable({
                ajax: window.location.pathname,
                columns: [
                    {data: 'employee_name'},
                    {data: 'start_date'},
                    {data: 'end_date'},
                    {data: 'days'},
                    {data: 'status'},
                    {data: 'action'}
                ]
            })
        })

        $(document).on('click', '.btn-approve', function () {

            let url = $(this).data('href');

            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0a8852',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(url, 'approved');
                }
            })
        })

        $(document).on('click', '.btn-reject', function () {

            let url = $(this).data('href');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#bd4967',
                cancelButtonColor: '#008da1',
                confirmButtonText: 'Yes, Reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(url, 'rejected');
                }
            })
        })

        function updateStatus(url, status) {
            $.ajax({
                url: url,
                data: {
                    status: status
                },
                method: 'PUT',
                success: function (data) {
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        if (table) {
                            table.ajax.reload();
                        }
                    }
                }
            })
        }

        $(document).on('click', '.view-btn', function () {
            let url = $(this).data('href');
            $('#view_modal').load(url, function () {
                $('#view_modal').modal('show');
            })
        })

    </script>
@endpush
