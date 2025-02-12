@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h4>Purchase Lists</h4>
                <a href="{{route('purchases.create')}}" class="btn btn-primary">
                    <i class="fa fa-plus"></i>
                    Add</a>
            </div>
        </div>
    </section>
    <section class="card">
        <div class="card-body">


            <table class="table" id="budget-table">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            let table = $('#budget-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '/purchases',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'title'},
                    {data: 'date'},
                    {data: 'amount'},
                    {data: 'status'},
                    {data: 'actions'}
                ],
            });

            $(document).on('click', '.delete-budget-btn', function () {

                let url = $(this).data('href');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'delete',
                            dataType: 'json',
                            success: function (res) {
                                console.log("deleted", res);
                                toastr.success("Item deleted");
                                table.ajax.reload();
                            },
                            error: function (er) {
                                console.log(er)
                            }
                        });

                    }
                })

            })

        })
    </script>
@endpush
