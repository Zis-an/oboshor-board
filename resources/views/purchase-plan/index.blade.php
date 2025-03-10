@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h4 class="text-capitalize">Purchase Plans</h4>
                <a href="{{route('purchase-plans.create')}}" class="btn btn-primary">
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
                <th>Fiscal Year</th>
                <th>Amount</th>
                <th>Date</th>
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
        $(document).ready(function(){

            let query = window.location.search;

            const urlParams = new URLSearchParams(query);

            let table = $('#budget-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '/purchase-plans',
                },
                columnDefs: [
                    {
                        targets: '_all',
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'financial_year.name', defaultContent: ''},
                    {data: 'amount'},
                    {data: 'date'},
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
