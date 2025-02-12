@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Reconciliations</h4>
                <button class="btn btn-primary create-modal-open-btn"
                        data-href="{{route('reconciliations.create')}}"
                >
                    <i class="fa fa-plus"></i>
                    Create
                </button>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="reconciliationTable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Account</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>


    <div class="modal fade" id="createReconciliationModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">

    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="updateReconciliationModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            //create modal open modal

            $(document).on('click', '.create-modal-open-btn', function () {
                $('#createReconciliationModal').load($(this).data('href'), function () {
                    $(this).modal('show')
                })
            });

            //validate form

            $('#createReconciliationModal').on('show.bs.modal', function () {
                $("#createReconciliationForm").validate({
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

            $('#updateReconciliationModal').on('show.bs.modal', function () {
                $("#updateReconciliationForm").validate({
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

            $(document).on('submit', 'form#createReconciliationForm', function (e) {
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

                        $('#createReconciliationModal').modal('hide');

                    },
                });
            });

            $(document).on('submit', 'form#updateReconciliationForm', function (e) {
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

                        $('#updateReconciliationModal').modal('hide');
                    },
                });
            });

            /*$("#ReconciliationTable").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');*/

            let table = $('#reconciliationTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '/reconciliations',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'date'},
                    {data: 'type'},
                    {data: 'account'},
                    {data: 'amount'},
                    {data: 'actions'}
                ],
            });


            //on click edit unit button

            $(document).on('click', '.edit-reconciliation-btn', function () {
                $('#updateReconciliationModal').load($(this).data('href'), function () {
                    $(this).modal('show')
                })
            });

            $(document).on('click', '.delete-reconciliation-btn', function () {

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

        });

    </script>
@endpush
