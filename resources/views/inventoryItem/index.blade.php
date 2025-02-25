@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="text-capitalize">Inventory Items</h4>
                @can('inventory.create')
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addNewModalId">
                        <i class="fa fa-plus"></i> Create
                    </button>
                @endcan
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="inventoryItemTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Head Item</th>
                        <th>Stock Qty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventoryItems as $inventoryItem)
                        <tr>
                            <td>{{ $inventoryItem->name }}</td>
                            <td>{{ $inventoryItem->headItem->name ?? 'N/A' }}</td>
                            <td>{{ $inventoryItem->stock_qty ?? '0' }}</td>
                            <td>
                                @can('inventory.edit')
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#editNewModalId{{ $inventoryItem->id }}">Edit</button>
                                @endcan
                                @can('inventory.delete')
                                <a href="{{ route('items.data.destroy', $inventoryItem->id) }}"
                                    class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#danger-header-modal{{ $inventoryItem->id }}">Delete</a>
                                @endcan
                            </td>
                            <!-- Edit Item Modal -->
                            <div class="modal fade" id="editNewModalId{{ $inventoryItem->id }}" tabindex="-1"
                                aria-labelledby="editNewModalLabel{{ $inventoryItem->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editNewModalLabel{{ $inventoryItem->id }}">Edit Item
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" action="{{ route('items.data.update', $inventoryItem->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="name{{ $inventoryItem->id }}"
                                                        class="form-label">Name*</label>
                                                    <input type="text" id="name{{ $inventoryItem->id }}" name="name"
                                                        class="form-control" value="{{ $inventoryItem->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="head_id{{ $inventoryItem->id }}"
                                                        class="form-label">Head*</label>
                                                    <select id="head_id{{ $inventoryItem->id }}" name="head_id"
                                                        class="form-select" required>
                                                        <option value="" selected>Select Account Head</option>
                                                        @foreach ($heads as $id => $name)
                                                            <option value="{{ $id }}"
                                                                {{ $inventoryItem->head_id == $id ? 'selected' : '' }}>
                                                                {{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="head_item_id{{ $inventoryItem->id }}"
                                                        class="form-label">Head Item*</label>
                                                    <select id="head_item_id{{ $inventoryItem->id }}" name="head_item_id"
                                                        class="form-select" required>
                                                        <option value="" selected>Select Head Item</option>
                                                        @foreach ($headItems as $id => $name)
                                                            <option value="{{ $id }}"
                                                                {{ $inventoryItem->head_item_id == $id ? 'selected' : '' }}>
                                                                {{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description{{ $inventoryItem->id }}"
                                                        class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2" placeholder="Description">{{ $inventoryItem->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div id="danger-header-modal{{ $inventoryItem->id }}" class="modal fade" tabindex="-1"
                                role="dialog" aria-labelledby="danger-header-modalLabel{{ $inventoryItem->id }}"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header modal-colored-header bg-danger">
                                            <h4 class="modal-title" id="danger-header-modalLabel{{ $inventoryItem->id }}">
                                                Delete</h4>
                                            <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="mt-0">Are You Sure You Want to Delete this?</h5>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <a href="{{ route('items.data.destroy', $inventoryItem->id) }}"
                                                class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



    <!-- Add Item Modal -->
    <div class="modal fade" id="addNewModalId" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewModalLabel">Add Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('items.data.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name*</label>
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Item Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="head_id" class="form-label">Account Head*</label>
                            <select id="head_id" name="head_id" class="form-select" required>
                                <option value="" selected>Select Account Head</option>
                                @foreach ($heads as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="head_item_id" class="form-label">Head Item*</label>
                            <select id="head_item_id" name="head_item_id" class="form-select" required>
                                <option value="" selected>Select Head Item</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function loadHeadItems(headId = null) {
                if (headId) {
                    $.ajax({
                        url: '/get-head-items/' + headId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            $('#head_item_id').empty();
                            $('#head_item_id').append('<option value="">Select Head Item</option>');
                            $.each(data, function(key, value) {
                                $('#head_item_id').append('<option value="' + key + '">' +
                                    value + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                        }
                    });
                } else {
                    $('#head_item_id').empty();
                    $('#head_item_id').append('<option value="">Select Head Item</option>');
                }
            }

            // Handle change event of Account Head dropdown
            $('#head_id').on('change', function() {
                var headId = $(this).val();
                loadHeadItems(headId);
            });
        });
    </script>
@endsection
