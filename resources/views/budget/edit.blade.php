@extends('layouts.app')
@push('css')
    <style>
        .add-btn{
            font-weight: bold;
            font-size: 16px;
        }
        #headsList {
            list-style-type: none;
            padding: 0;
        }
        .list-group-item {
            align-items: center;
            font-size: 16px;
            padding: 12px 20px;
            border: 1px solid #ddd;
        }
        .custom-checkbox {
            transform: scale(1.3);
            cursor: pointer;
        }
        .head-item {
            font-style: italic;
        }
        .modal-lg {
            max-width: 800px;
        }
    </style>
@endpush
@section('main')
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h4>Update Budget</h4>
        </div>
    </section>
    <section class="card">
        @include('partials.error-alert', ['errors' => $errors])
        {!! Form::open(['url' => route('budgets.update', $budget->id), 'method' => 'POST']) !!}
        @method('PUT')
        @csrf
        {!! Form::hidden('selected_items', '', ['id' => 'selectedItemsInput']) !!}
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{Form::label('financial_year_id', 'Financial Year*')}}
                    {{Form::select('financial_year_id', $financialYears, $budget->financial_year_id, ['class' => 'form-control', 'placeholder' => 'Select Financial Year', 'disabled' => 'disabled'])}}
                </div>
                <div class="col-6 align-content-center">
                    <div class="d-flex justify-content-end mr-5">
                        <button type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addBudgetModal">Add Head & Head Items</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" id="budgetContainer">
            <div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="budgetItemsBody">
                    @foreach($budgetItems as $budgetItem)
                        <tr>
                            @if($budgetItem->parent_id == null)
                                <td class="font-weight-bold">{{ $budgetItem->head ? $budgetItem->head->name:'' }}</td>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="{{ $budgetItem->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @else
                                <td class="pl-5 font-italic">{{ $budgetItem->headItem ? $budgetItem->headItem->name:'' }}</td>
                                <td>
                                    <input type="hidden" name="budget_items[{{ $budgetItem->id }}][id]" value="{{ $budgetItem->id }}">
                                    <input type="text" class="form-control td-amount"
                                           name="budget_items[{{ $budgetItem->id }}][amount]"
                                           value="{{ $budgetItem->amount }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item" data-item-id="{{ $budgetItem->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-2">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
        {!! Form::close() !!}
    </section>
    <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel">Select Heads & Items</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="headsList" class="list-group">
                        @foreach($heads as $head)
                            @php
                                // Find all selected head items under this head
                                $selectedItems = $budgetItems->where('head_id', $head->id)->pluck('head_item_id')->toArray();
                                $isHeadChecked = count($selectedItems) > 0 && count($selectedItems) === $head->items->count();
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="head-name">
                                    <strong>{{ $head->name }}</strong>
                                </div>
                                <input type="checkbox" class="custom-checkbox head-checkbox" data-head-id="{{ $head->id }}"
                                        {{ $isHeadChecked ? 'checked' : '' }}>
                            </li>
                            @foreach($head->items as $item)
                                @php
                                    // Get the amount for this head-item from budgetItems
                                    $itemAmount = $budgetItems->where('head_id', $head->id)
                                                              ->where('head_item_id', $item->id)
                                                              ->first()->amount ?? '';
                                @endphp
                                <li class="list-group-item align-items-center head-item">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="item-name">
                                                <span class="text-muted font-italic">{{ $item->name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <!-- Amount input field with fixed size and amount pre-filled -->
                                            <input type="number" class="form-control amount-input w-100" placeholder="Enter amount"
                                                   data-item-id="{{ $item->id }}" value="{{ $itemAmount }}"
                                                   style="width: 120px; margin: 0 10px; height: 35px;">
                                        </div>
                                        <div class="col-md-1 d-flex justify-content-end">
                                            <input type="checkbox" class="custom-checkbox item-checkbox"
                                                   data-item-id="{{ $item->id }}"
                                                   data-head-id="{{ $head->id }}"
                                                   data-amount="{{ $itemAmount }}"
                                                    {{ in_array($item->id, $selectedItems) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="applySelection">Apply</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            // When a head checkbox is checked/unchecked
            $('.head-checkbox').on('change', function () {
                let headId = $(this).data('head-id');
                let isChecked = $(this).prop('checked');

                // Find all child items of this head and check/uncheck them
                $('.item-checkbox[data-head-id="' + headId + '"]').prop('checked', isChecked);
            });

            // If all items under a head are checked, check the head checkbox
            $('.item-checkbox').on('change', function () {
                let headId = $(this).data('head-id');
                let allItems = $('.item-checkbox[data-head-id="' + headId + '"]');
                let checkedItems = allItems.filter(':checked');

                // If all child items are checked, check the head; otherwise, uncheck it
                $('.head-checkbox[data-head-id="' + headId + '"]').prop('checked', allItems.length === checkedItems.length);
            });

            // When the apply button is clicked, collect selected items and update the table
            $('#applySelection').on('click', function () {
                let selectedItems = [];

                // Loop through each checked item checkbox
                $('.item-checkbox:checked').each(function () {
                    let headId = $(this).data('head-id');
                    let itemId = $(this).data('item-id');
                    let amount = $(this).closest('.list-group-item').find('.amount-input').val();

                    // Ensure itemId and amount are valid
                    if (itemId && amount) {
                        // Find the head name correctly by traversing up to the parent head name element
                        let headName = $(this).closest('.head-item').prev('.list-group-item').find('.head-name strong').text();

                        // Check if the headId already exists in the selectedItems array
                        let existingHead = selectedItems.find(item => item.HeadId === headId);

                        if (existingHead) {
                            // If the head already exists, just push the new item under the same head
                            existingHead.Items.push({
                                ItemId: itemId,
                                ItemName: $(this).closest('.list-group-item').find('.text-muted').text(),
                                Amount: amount
                            });
                        } else {
                            // If the head doesn't exist, create a new entry for the head with the item
                            selectedItems.push({
                                HeadId: headId,
                                HeadName: headName,
                                Items: [{
                                    ItemId: itemId,
                                    ItemName: $(this).closest('.list-group-item').find('.text-muted').text(),
                                    Amount: amount
                                }]
                            });
                        }
                    }
                });

                if (selectedItems.length > 0) {
                    console.log('selectedItems:', selectedItems);
                } else {
                    console.log('No items were selected.');
                }

                // If there are no selected items, don't proceed
                if (selectedItems.length === 0) {
                    alert('Please select at least one item');
                    return;
                }

                // Add selected items to hidden input
                let selectedItemsInput = $('#selectedItemsInput');
                selectedItemsInput.val(JSON.stringify(selectedItems));

                // Function to update the table
                updateBudgetTable(selectedItems);

                // Close the modal
                $('#addBudgetModal').modal('hide');

            });
            // Function to update the budget table dynamically
            function updateBudgetTable(selectedItems) {
                let tableBody = $('#budgetItemsBody');
                tableBody.empty(); // Clear existing rows
                selectedItems.forEach(headGroup => {
                    // Add Head Row
                    tableBody.append(`
                        <tr class="font-weight-bold" data-head-id="${headGroup.HeadId}">
                            <td>${headGroup.HeadName}</td>
                            <td></td>
                        </tr>
                    `);
                    headGroup.Items.forEach(item => {
                        tableBody.append(`
                            <tr data-item-id="${item.ItemId}">
                                <td class="pl-5 font-italic">${item.ItemName}</td> <!-- Move classes to td -->
                                    <td>
                                        <input type="text"
                                               class="form-control td-amount"
                                               value="${item.Amount}"
                                               name="budget_items[${item.ItemId}][amount]" /> <!-- Adjust name attribute -->
                                    </td>
                            </tr>
                        `);
                    });
                });
            }
        });
    </script>

    {{-- Head Items Deletion Related Functionality --}}
    <script>
        $(document).on('click', '.remove-item', function () {
            let itemId = $(this).data('item-id');
            let row = $(this).closest('tr');

            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: "{{ route('budget-items.destroy') }}",
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        _token: "{{ csrf_token() }}",
                        id: itemId
                    },
                    success: function (response) {
                        if (response.success) {
                            row.remove(); // Remove row from table
                            $('#total-budget').text(response.new_budget_amount.toFixed(2)); // Update budget amount
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function () {
                        alert("Something went wrong.");
                    }
                });
            }
        });
    </script>
@endpush
