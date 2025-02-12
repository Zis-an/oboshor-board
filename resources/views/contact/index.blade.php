@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="text-capitalize">{{request()->query('type')}}s</h4>
                <button class="btn btn-primary create-modal-open-btn">
                    <i class="fa fa-plus"></i>
                    Create</button>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="employeeTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>


    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            {!! Form::open(['url' => route('contacts.store'), 'id' => 'createEmployeeForm']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Add {{request()->query('type')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="my-2">
                        {!! Form::label('name', 'Name*') !!}
                        {!! Form::text('name', '', ['class'=>'form-control', 'placeholder' => 'Name']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('phone', 'Phone*') !!}
                        {!! Form::text('phone', '', ['class'=>'form-control', 'placeholder' => 'Phone']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('address', 'Address*') !!}
                        {!! Form::text('address', '', ['class'=>'form-control', 'placeholder' => 'Address']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('email', 'Email') !!}
                        {!! Form::text('email', '', ['class'=>'form-control', 'placeholder' => 'Email']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('website', 'Website') !!}
                        {!! Form::text('website', '', ['class'=>'form-control', 'placeholder' => 'Website']) !!}
                    </div>

                    <div>
                        <input type="hidden" name="type" value="{{request()->query('type')}}">
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

            let type = new URLSearchParams(window.location.search).get('type');

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
                            toastr.error(message);
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

            let table = $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: `/contacts?type=${type}`,
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'name'},
                    {data: 'address'},
                    {data: 'phone'},
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
