@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Incomes</h4>
                <a class="btn btn-primary"
                   href="{{route('incomes.create')}}"
                >
                    <i class="fa fa-plus"></i>
                    Create
                </a>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="incomeHeadTable">
                <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="updateIncomeModal" tabindex="-1" aria-hidden="true">
    </div>

    <!--View Modal -->
    <div class="modal fade" id="viewIncomeModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            //validate form

            $('#createExpenseHeadModal').on('show.bs.modal', function () {
                $("#createExpenseHeadForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        short: {
                            required: true,
                        }
                    },
                    messages: {}
                });
            });

            //update form

            $('#updateIncomeModal').on('show.bs.modal', function () {
                $("#updateIncomeForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        short: {
                            required: true,
                            type: String,
                        }
                    },
                    messages: {}
                });
            });

            //add unit

            $(document).on('submit', 'form#createIncomeForm', function (e) {
                e.preventDefault();

                $(this)
                    .find('button[type="submit"]')
                    .attr('disabled', true);
                //use form data instead of serialize in file upload
                const data = new FormData(this);
                //returns array
                //data.append('image', $('#upload-image')[0].files);

                //console.log('data', $('#upload-image')[0].files);

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        let {status, message} = response;

                        if (status === 'success') {
                            toastr.success(message);
                            table.ajax.reload();
                        } else {
                            toastr.error(message);
                        }

                        $('#createExpenseHeadModal').modal('hide');

                    },
                });
            });

            $(document).on('submit', 'form#updateIncomeForm', function (e) {
                e.preventDefault();

                $(this)
                    .find('button[type="submit"]')
                    .attr('disabled', true);
                //use form data instead of serialize in file upload
                const data = new FormData(this);
                //returns array
                //data.append('image', $('#upload-image')[0].files);

                //console.log('data', $('#upload-image')[0].files);

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        let {status, message} = response;

                        if (status === 'success') {
                            toastr.success(message);
                            table.ajax.reload();
                            $('#updateIncomeModal').modal('hide');

                        } else {
                            toastr.error(message);
                        }
                    },
                });
            });

            /*$("#ExpenseHeadTable").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');*/

            let table = $('#incomeHeadTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '/incomes',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'description'},
                    {data: 'amount'},
                    {data: 'date'},
                    {data: 'status'},
                    {data: 'actions'}
                ],
            });


            //on click edit unit button

            $(document).on('click', '.edit-income-btn', function () {
                $('#updateIncomeModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.delete-income-btn', function () {

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

            //view
            $(document).on('click', '.view-income-btn', function () {
                $('#viewIncomeModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

        });

    </script>
@endpush
