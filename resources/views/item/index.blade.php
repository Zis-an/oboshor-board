@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="text-capitalize">Purchase Items</h4>
                <div>
                    <a href="{{route('item-requests.create')}}">Request For Item</a>
                    @can('purchase.create')
                        <button class="btn btn-primary create-modal-open-btn"
                                data-href="{{route('items.create')}}"
                        >
                            <i class="fa fa-plus"></i>
                            Create
                        </button>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="headItemTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Head Item</th>
                    <th>Stock Qty</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>

    <div class="modal fade" id="createHeadItemModal" tabindex="-1" aria-hidden="true">
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="updateHeadItemModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            //create modal open modal

            $(document).on('click', '.create-modal-open-btn', function () {
                $('#createHeadItemModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            //validate form

            $('#createHeadItemModal').on('show.bs.modal', function () {
                $("#createHeadItemForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        head_id: {
                            required: true,
                        },
                    },
                    messages: {}
                });
            });

            //update form

            $('#updateHeadItemModal').on('show.bs.modal', function () {
                $("#updateHeadItemForm").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        head_id: {
                            required: true,
                        },
                    },
                    messages: {}
                });
            });

            //add unit

            $(document).on('submit', 'form#createHeadItemForm', function (e) {
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
                            $(this).trigger('reset');
                        } else {
                            toastr.error(message);
                        }

                        $('#createHeadItemModal').modal('hide');

                    },
                });
            });

            $(document).on('submit', 'form#updateHeadItemForm', function (e) {
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
                            $(this).trigger('reset');
                        } else {
                            toastr.error(message);
                        }

                        $('#updateHeadItemModal').modal('hide');
                    },
                });
            });

            let url = window.location.pathname;

            let table = $('#headItemTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: url,
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'name'},
                    {data: 'head_item.name'},
                    {data: 'stock_qty', searchable: false},
                    {data: 'actions'}
                ],
            });


            //on click edit unit button

            $(document).on('click', '.edit-head-item-btn', function () {
                $('#updateHeadItemModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.delete-head-item-btn', function () {

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

            $(document).on('change', '#selectHead', function () {

                let head = this.value;

                $.ajax({
                    url: `/get-head-items?head_id=${head}`,
                    success: function (data) {

                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let options = '<option>Select Item Head</option>';
                        data.map(item => {
                            options += `<option value='${item.id}'>${item.name}</option>`
                        })

                        $('#selectHeadItem').html(options);

                    },
                    error: function () {

                    }
                })
            })

        });

    </script>
@endpush
