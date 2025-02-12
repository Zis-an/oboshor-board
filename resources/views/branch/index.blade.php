@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Branch List</h4>
                <button class="btn btn-primary create-modal-open-btn">
                    <i class="fa fa-plus"></i>
                    Create</button>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="branchTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Branch Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>


    <div class="modal fade" id="createBranchModal" tabindex="-1" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            {!! Form::open(['url' => route('branches.store'), 'id' => 'createBranchForm']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Add Branch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="my-2">
                        {!! Form::label('name', 'Name*') !!}
                        {!! Form::text('name', '', ['class'=>'form-control']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('bank_id', 'Bank*', ['class' => 'form-label']) !!}
                        {!! Form::select('bank_id', $banks, '', ['class' => 'form-control', 'placeholder' => 'Select Branch']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('phone', 'Phone*') !!}
                        {!! Form::text('phone', '', ['class'=>'form-control']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('email', 'Email*') !!}
                        {!! Form::text('email', '', ['class'=>'form-control']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('address', 'Address*') !!}
                        {!! Form::text('address', '', ['class'=>'form-control']) !!}
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
    <div class="modal fade" id="updateBranchModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            //create modal open modal

            $(document).on('click', '.create-modal-open-btn', function () {
                $('#createBranchModal').modal('show');
            });

            //validate form

            $('#createBranchModal').on('show.bs.modal', function () {
                $("#createBranchForm").validate({
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

            $('#updateBranchModal').on('show.bs.modal', function () {
                $("#updateBranchForm").validate({
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

            $(document).on('submit', 'form#createBranchForm', function (e) {
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

                        $('#createBranchModal').modal('hide');

                    },
                });
            });

            $(document).on('submit', 'form#updateBranchForm', function (e) {
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

                        $('#updateBranchModal').modal('hide');
                    },
                });
            });

            /*$("#BranchTable").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');*/

            let table = $('#branchTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: '/branches',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'name'},
                    {data: 'bank.name'},
                    {data: 'phone'},
                    {data: 'email'},
                    {data: 'address'},
                    {data: 'created_by.name', searchable: false},
                    {data: 'actions'}
                ],
            });


            //on click edit unit button

            $(document).on('click', '.edit-branch-btn', function () {
                $('#updateBranchModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.delete-branch-btn', function () {

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
