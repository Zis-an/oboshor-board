@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Employee List</h4>
                <button class="btn btn-primary create-modal-open-btn">
                    <i class="fa fa-plus"></i>
                    Create
                </button>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="employeeTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Salary</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>


    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            {!! Form::open(['url' => route('employees.store'), 'id' => 'createEmployeeForm']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Add Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('name', 'Name*') !!}
                                {!! Form::text('name', '', ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('designation_id', 'Select Designaiton*', ['class' => 'form-label']) !!}
                                {!! Form::select('designation_id', $designations, '', ['class' => 'form-control', 'placeholder' => 'Select Designation']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('user_id', 'User Id*') !!}
                                {!! Form::text('user_id', '', ['class'=>'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('password', 'Password*') !!}
                                {!! Form::text('password', '', ['class'=>'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('salary_type', 'Salary Type*', ['class' => 'form-label']) !!}
                                {!! Form::select('salary_type', [ 'monthly' => 'Monthly', 'daily' => 'Daily' ], null, ['class' => 'form-control', 'placeholder' => 'Select Salary Type']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('salary', 'Salary*') !!}
                                {!! Form::text('salary', '', ['class'=>'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('phone', 'Phone*') !!}
                                {!! Form::text('phone', '', ['class'=>'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('email', 'Email*') !!}
                                {!! Form::text('email', '', ['class'=>'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="my-2">
                                {!! Form::label('address', 'Address*') !!}
                                {!! Form::text('address', '', ['class'=>'form-control']) !!}
                            </div>
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
    <div class="modal fade" id="updateEmployeeModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            //create modal open modal

            $(document).on('click', '.create-modal-open-btn', function () {
                $('#createEmployeeModal').modal('show');
            });

            //validate form

            $('#createEmployeeModal').on('show.bs.modal', function () {
                $("#createEmployeeForm").validate({
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

            $('#updateEmployeeModal').on('show.bs.modal', function () {
                $("#updateEmployeeForm").validate({
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

            $(document).on('submit', 'form#createEmployeeForm', function (e) {
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

                        $('#createEmployeeModal').modal('hide');

                    },
                });
            });

            $(document).on('submit', 'form#updateEmployeeForm', function (e) {
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

                        $('#updateEmployeeModal').modal('hide');
                    },
                });
            });

            /*$("#EmployeeTable").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');*/

            let table = $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '/employees',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'name'},
                    {data: 'designation.name'},
                    {data: 'salary'},
                    {data: 'phone'},
                    {data: 'email'},
                    {data: 'address'},
                    {data: 'actions'}
                ],
            });


            //on click edit unit button

            $(document).on('click', '.edit-employee-btn', function () {
                $('#updateEmployeeModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.delete-employee-btn', function () {

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
