@extends('layouts.app')
@push('css')
    <style>
        #headsList ul {
            list-style-type: none; /* Removes bullets from the list */
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            font-size: 16px;
        }
        .custom-checkbox {
            transform: scale(1.3); /* Make checkboxes slightly bigger */
        }
        .head-item {
            padding-left: 30px;
        }
    </style>
@endpush
@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h4 class="text-capitalize">{{request()->query('type')}} Budgets</h4>
                <a href="{{route('budgets.create', ['type' => request()->query('type')])}}" class="btn btn-primary">
                    <i class="fa fa-plus"></i>
                    &nbsp;Add</a>
            </div>
        </div>
    </section>

    <section class="card">

        <div class="card-body">

            <table class="table" id="budget-table">
                <thead>
                <tr>
                    <th>Financial Year</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>

    {{-- Newly Added --}}
    <div class="modal fade" id="budgetModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel">Heads & Items</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="headsList" class="list-group"></ul> <!-- Will be populated dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            let query = window.location.search;

            const urlParams = new URLSearchParams(query);

            let table = $('#budget-table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '/budgets',
                    data: {type: urlParams.get('type')}
                },
                columnDefs: [
                    {
                        targets: '_all',
                        orderable: false,
                        searchable: false,
                        defaultContent: ''
                    },
                ],
                columns: [
                    {data: 'financial_year.name'},
                    {data: 'amount'},
                    {data: 'actions'}
                ],
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            }).buttons().container().appendTo('#budget-table .col-md-6:eq(0)');


            $(document).on('click', '.delete-budget-btn', function () {

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



        })
    </script>

    <script>
        $(document).on('click', '.open-budget-modal', function () {
            // Fetch heads with their status and items when modal is opened
            $.ajax({
                url: '/get-heads-with-status',
                type: 'GET',
                success: function (response) {
                    let listHtml = '';
                    response.heads.forEach(head => {
                        // Create the head checkbox with label
                        //listHtml += `<li><label><input type="checkbox" class="head-checkbox" data-head-id="${head.id}" ${head.status === 1 ? 'checked' : ''}> <strong>${head.name}</strong></label><ul>`;
                        listHtml += `
                        <li class="list-group-item bg-light">
                            <span><strong>${head.name}</strong></span>
                            <input type="checkbox" class="head-checkbox custom-checkbox" data-head-id="${head.id}" ${head.status === 1 ? 'checked' : ''}>
                        </li>`;
                        // Create items checkboxes under the head
                        head.items.forEach(item => {
                            //listHtml += `<li><label><input type="checkbox" class="item-checkbox" data-item-id="${item.id}" data-head-id="${head.id}" ${item.status === 1 ? 'checked' : ''}> ${item.name}</label></li>`;
                            listHtml += `
                            <li class="list-group-item head-item">
                                <span>${item.name}</span>
                                <input type="checkbox" class="item-checkbox custom-checkbox" data-item-id="${item.id}" data-head-id="${head.id}" ${item.status === 1 ? 'checked' : ''}>
                            </li>`;

                        });

                        //listHtml += `</ul></li>`;
                    });

                    $('#headsList').html(listHtml);
                    $('#budgetModal').modal('show');
                },
                error: function () {
                    alert('Failed to fetch data.');
                }
            });
        });

        // Handle head checkbox click to select/deselect all items under that head
        $(document).on('change', '.head-checkbox', function () {
            var headId = $(this).data('head-id');
            var isChecked = $(this).prop('checked');
            var selectedHeadItems = [];
            var uncheckedHeadItems = [];


            // Select/deselect all items under the clicked head
            $(`.item-checkbox[data-head-id="${headId}"]`).each(function() {
                $(this).prop('checked', isChecked);
                var itemId = $(this).data('item-id');
                if (isChecked) {
                    selectedHeadItems.push(itemId);
                } else {
                    uncheckedHeadItems.push(itemId);
                }
            });

            // Collect selected items under the head
            var selectedHeadItems = isChecked ? $(`.item-checkbox[data-head-id="${headId}"]`).map(function() {
                return $(this).data('item-id');
            }).get() : [];

            // Send AJAX request to update the status of the head and selected items
            $.ajax({
                url: '/update-status',
                type: 'POST',
                data: {
                    head_item_ids: selectedHeadItems,
                    unchecked_head_item_ids: uncheckedHeadItems,
                    head_ids: isChecked ? [headId] : [],
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    console.log('Status updated successfully');
                },
                error: function () {
                    alert('Failed to update status.');
                }
            });
        });

        // Handle individual item checkbox click to select/deselect item
        $(document).on('change', '.item-checkbox', function () {
            var itemId = $(this).data('item-id');
            var headId = $(this).data('head-id');
            var isChecked = $(this).prop('checked');

            // Send AJAX request to update the status of the item
            $.ajax({
                url: '/update-status',
                type: 'POST',
                data: {
                    head_item_ids: isChecked ? [itemId] : [],
                    unchecked_head_item_ids: isChecked ? [] : [itemId],
                    head_ids: [],
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    console.log('Item status updated successfully');
                },
                error: function () {
                    alert('Failed to update item status.');
                }
            });
        });

        // Handle item checkbox click to select/deselect individual item
        $(document).on('click', '.item-checkbox', function () {
            var itemId = $(this).data('item-id');
            var headId = $(this).data('head-id');
            var selectedHeadItems = [itemId];

            // Send AJAX request to update the status of the item
            $.ajax({
                url: '/update-status',
                type: 'POST',
                data: {
                    head_item_ids: selectedHeadItems,
                    head_ids: [],
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    console.log('Item status updated successfully');
                },
                error: function () {
                    alert('Failed to update item status.');
                }
            });
        });
    </script>


@endpush
