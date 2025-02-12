@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Expense Heads</h4>

                <a href="{{ route('arrange-expense-head', $type) }}" target="_blank" class="btn btn-info">Arrange Table Head</a>

                <button class="btn btn-primary create-modal-open-btn">
                    <i class="fa fa-plus"></i>
                    Create
                </button> 

            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="headTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>

    <input id="headType" type="hidden" value="{{ $type }}">

    <div class="modal fade" id="createHeadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            {!! Form::open(['url' => route('heads.store'), 'id' => 'createHeadForm']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Add Expense Head</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="my-2">
                        {!! Form::label('name', 'Name*') !!}
                        {!! Form::text('name', '', ['class' => 'form-control']) !!}
                    </div>

                    <div class="my-2">
                        {!! Form::label('description', 'Description*') !!}
                        {!! Form::textarea('description', '', ['class' => 'form-control']) !!}
                    </div>

                    @if ($type == 'expense')
                        <div class="my-2">
                            <label>
                                {!! Form::checkbox('is_office_expense', 1) !!}
                                <span>Is Office Expense</span>
                            </label>
                        </div>
                    @endif


                    <input type="hidden" name="type" value="{{ $type }}" />

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="updateHeadModal" tabindex="-1" aria-hidden="true">
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            //create modal open modal

            $(document).on('click', '.create-modal-open-btn', function() {
                $('#createHeadModal').modal('show');
            });

            //validate form

            $('#createHeadModal').on('show.bs.modal', function() {
                $("#createHeadForm").validate({
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

            $('#updateHeadModal').on('show.bs.modal', function() {
                $("#updateHeadForm").validate({
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

            $(document).on('submit', 'form#createHeadForm', function(e) {
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
                    success: function(response) {

                        let {
                            status,
                            message
                        } = response;

                        if (status === 'success') {
                            toastr.success(message);
                            table.ajax.reload();
                        } else {
                            toastr.warn(message);
                        }

                        $('#createHeadModal').modal('hide');

                    },
                });
            });

            $(document).on('submit', 'form#updateHeadForm', function(e) {
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
                    success: function(response) {
                        let {
                            status,
                            message
                        } = response;

                        if (status === 'success') {
                            toastr.success(message);
                            table.ajax.reload();

                        } else {
                            toastr.warn(message);
                        }

                        $('#updateHeadModal').modal('hide');
                    },
                });
            });

            //get type from url

            let url = window.location.pathname;

            console.log({
                url
            });

            let table = $('#headTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: url,
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                }, ],
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'actions'
                    }
                ],
            });


            //on click edit unit button

            $(document).on('click', '.edit-head-btn', function() {
                $('#updateHeadModal').load($(this).data('href'), function(result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.delete-head-btn', function() {

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
                            success: function(res) {
                                console.log("deleted", res);
                                toastr.success("Item deleted");
                                table.ajax.reload();
                            },
                            error: function(er) {
                                console.log(er)
                            }
                        });

                    }
                })

            })

        });
    </script>
@endpush