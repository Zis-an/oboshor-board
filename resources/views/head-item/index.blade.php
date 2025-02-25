@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="text-capitalize">{{ $type }} Head Items</h4>

                <div>
                    <form action="{{ url()->current() }}" method="get" id="head_filter_form">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="form-group">
                            <label for="head_id">Head</label>
                            <select name="head_id" id="head_id" class="form-control">
                                <option value="">Select One</option>
                                @foreach ($heads as $head)
                                    <option value="{{ $head->id }}" @if ($searched_head_id == $head->id) selected @endif>
                                        {{ $head->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <a href="{{ route('arrange-expense-head-item', $type) }}" class="btn btn-info">Arrange Table
                    Head</a>


                <button class="btn btn-primary create-modal-open-btn"
                        data-href="{{ route('head-items.create', ['type' => $type]) }}">
                    <i class="fa fa-plus"></i>
                    Create
                </button>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="headItemTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Head</th>
                    <th>Description</th>
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
        $(document).ready(function() {

            // Create modal open modal
            $(document).on('click', '.create-modal-open-btn', function() {
                $('#createHeadItemModal').load($(this).data('href'), function(result) {
                    $(this).modal('show');
                });
            });

            // Handle form change to trigger the filter without reloading the page
            $('#head_id').on('change', function() {
                // Manually trigger form submission without page reload
                $('#head_filter_form').submit();
            });

            // Intercept form submission using Ajax
            $('#head_filter_form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                var formData = $(this).serialize(); // Serialize the form data

                // Send the filter parameters to the server via Ajax
                $.ajax({
                    method: 'GET',
                    url: window.location.pathname, // Current URL (same page)
                    data: formData, // Send the serialized data (including the selected head_id)
                    success: function(response) {
                        // Update the DataTable with new filtered data
                        table.clear().rows.add(response.data).draw();
                    }
                });
            });

            // Initialize the DataTable with the filtered data
            let table = $('#headItemTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: window.location.pathname, // Use the current URL to load filtered data
                    data: function(d) {
                        // Add filter parameters (head_id) to the DataTable request
                        d.head_id = $('#head_id').val();
                    }
                },
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                }],
                columns: [{
                    data: 'name'
                },
                    {
                        data: 'head.name'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'actions'
                    }
                ]
            });

            // Handle the actions (Edit, Delete) for each row in DataTable
            $(document).on('click', '.edit-head-item-btn', function() {
                $('#updateHeadItemModal').load($(this).data('href'), function(result) {
                    $(this).modal('show');
                });
            });

            $(document).on('click', '.delete-head-item-btn', function() {
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
                                toastr.success("Item deleted");
                                table.ajax.reload();
                            },
                            error: function(er) {
                                console.log(er);
                            }
                        });
                    }
                });
            });

        });
    </script>
    {{-- create --}}
    <script>
        $(document).on('submit', 'form#createHeadItemForm', function(e) {
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
                        $(this).trigger('reset');
                    } else {
                        toastr.error(message);
                    }

                    $('#createHeadItemModal').modal('hide');

                },
            });
        });
    </script>
    {{-- update --}}
    <script>
        $(document).on('submit', 'form#updateHeadItemForm', function(e) {
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
                        $(this).trigger('reset');
                    } else {
                        toastr.error(message);
                    }

                    $('#updateHeadItemModal').modal('hide');
                },
            });
        });
    </script>
@endpush
