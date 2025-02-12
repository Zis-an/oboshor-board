@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Account Types</h4>
                <button class="btn btn-primary create-modal-open-btn">
                    <i class="fa fa-plus"></i>
                    Create
                </button>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="accountTypeTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>


    <div class="modal fade" id="createAccountTypeModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            {!! Form::open(['url' => route('account-types.store'), 'id' => 'createAccountTypeForm', 'files' => true]) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Add Account Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12">
                            {!! Form::label('name', 'Name*') !!}
                            {!! Form::text('name', '', ['class'=>'form-control']) !!}
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="form-check">
                                <label>
                                {!! Form::checkbox('allow_withdraw', '1', 0, ['class'=> 'form-check-input']) !!}
                                Allow Withdraw
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="form-check">
                                <label>
                                    {!! Form::checkbox('allow_deposit', '1', 0, ['class'=> 'form-check-input']) !!}
                                    Allow Deposit
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="form-check">
                                <label>
                                    {!! Form::checkbox('has_interest', '1', 0, ['class'=> 'form-check-input']) !!}
                                    Has Interest
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="form-check">
                                <label>
                                    {!! Form::checkbox('has_maturity_period', '1', 0, ['class'=> 'form-check-input']) !!}
                                    Has Maturity Period
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            {!! Form::label('description', 'Description*') !!}
                            {!! Form::textarea('description', '', ['class'=>'form-control', 'rows' => 2]) !!}
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="updateAccountTypeModal" tabindex="-1" aria-hidden="true">
    </div>
    <!-- View Modal -->
    <div class="modal fade" id="viewAccountTypeModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            //create modal open modal

            $(document).on('click', '.create-modal-open-btn', function () {
                $('#createAccountTypeModal').modal('show');
            });

            //add unit

            $(document).on('submit', 'form#createAccountTypeForm', function (e) {
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
                            toastr.warn(message);
                        }

                        $('#createAccountTypeModal').modal('hide');

                    },
                });
            });

            $(document).on('submit', 'form#updateAccountTypeForm', function (e) {
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
                            toastr.warn(message);
                        }

                        $('#updateAccountTypeModal').modal('hide');
                    },
                });
            });

            /*$("#AccountTypeTable").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');*/

            let table = $('#accountTypeTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '/account-types',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'name'},
                    {data: 'description'},
                    {data: 'created_by.name', searchable: false},
                    {data: 'actions'}
                ],
            });

            $('#createAccountTypeModal').on('show.bs.modal', function () {
                $("#createAccountTypeForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                    },
                    messages: {}
                });
            });

            $('#updateAccountTypeModal').on('show.bs.modal', function () {
                $("#updateAccountTypeForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        description: {
                            required: true,
                            type: String,
                        }
                    },
                    messages: {}
                });
            });

            //on click edit unit button

            $(document).on('click', '.edit-account-type-btn', function () {
                $('#updateAccountTypeModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            //on Click view button

            $(document).on('click', '.view-account-type-btn', function () {
                $('#viewAccountTypeModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.delete-account-type-btn', function () {

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
