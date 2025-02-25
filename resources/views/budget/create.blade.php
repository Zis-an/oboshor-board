@extends('layouts.app')
@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4 class="text-capitalize">Add {{request()->query('type')}} Budget</h4>
        </div>
    </section>
    <section class="card">
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div>{{$error}}</div>
            @endforeach
        @endif
        {!! Form::open(['url' => route('budgets.store')]) !!}
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{Form::label('year_id', 'Financial Years')}}
                    {{Form::select('year_id', $financialYears, '', ['class' => 'form-control', 'placeholder' => 'Select Financail Year', 'required'])}}
                </div>
                {!! Form::hidden('amount', 0, ['class' => 'form-control', 'id' => 'total-amount', 'readonly', 'required']) !!}
            </div>
        </div>
        <input type="hidden" name="type" value="{{request()->query('type')}}"/>
        <div id="budget_form"></div>
        {!! Form::close() !!}
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change', '.sub-total', function(){
                let amount = 0;
                $('#budget_container .sub-total').each((a, el) => {
                    amount += Number($(el).val());
                })
                $('#total-amount').val(amount);
                // Update the hidden form field with the total amount
                $('input[name="amount"]').val(amount);
            });

            $(document).on('change', '.td-amount', function () {
                let amount = 0;
                let subTotalRow = $(this).closest('tr').prevAll(".sub-total-row:first");
                let subTotal = 0;
                $(subTotalRow).nextUntil('tr.sub-total-row').each(function(a, el){
                    subTotal += Number(($(el).find('.td-amount')).val());
                })

                $(subTotalRow).find('.sub-total').val(subTotal)
                $('#budget_container .sub-total').each((a, el) => {
                    amount += Number($(el).val());
                })
                $('#total-amount').val(amount);
                // Update the hidden form field with the total amount
                $('input[name="amount"]').val(amount);
            });

            $('#year_id').on('change', function(){
                let id = this.value;

                $.ajax({
                    url: window.location.href,
                    data: {fy: id},
                    success: function(html){
                        $("#budget_form").empty(); // Clear the previous content
                        $("#budget_form").html(html);

                        // Newly Added Part: Fetch Heads & Items
                        fetchHeadsAndItems(id);
                    }
                })
            });

            // Newly Added Part
            // Function to fetch heads & items and show modal
            let selectedHeads = {}; // Object to track selected heads and items

            function fetchHeadsAndItems() {
                $.ajax({
                    url: '/get-heads-with-status', // Ensure this endpoint returns all heads/items
                    type: 'GET',
                    success: function (response) {
                        let listHtml = '';
                        response.heads.forEach(head => {
                            listHtml += `
                <li class="list-group-item bg-light">
                    <span><strong>${head.name}</strong></span>
                    <input type="checkbox" class="head-checkbox custom-checkbox" data-head-id="${head.id}" ${head.status === 1 ? 'checked' : ''}>
                </li>`;
                            head.items.forEach(item => {
                                listHtml += `
                <li class="list-group-item head-item">
                    <span>${item.name}</span>
                    <input type="checkbox" class="item-checkbox custom-checkbox" data-item-id="${item.id}" data-head-id="${head.id}" ${item.status === 1 ? 'checked' : ''}>
                </li>`;
                            });
                        });

                        $('#headsList').html(listHtml);
                        $('#headsModal').modal('show'); // Open the modal after populating data

                        // Attach event listeners after adding checkboxes
                        attachCheckboxListeners();
                    },
                    error: function () {
                        alert('Failed to fetch data.');
                    }
                });
            }

            // Function to handle checkbox interactions
            function attachCheckboxListeners() {
                // When a head checkbox is clicked
                $(document).off('change', '.head-checkbox').on('change', '.head-checkbox', function () {
                    let headId = $(this).data('head-id');
                    let isChecked = $(this).prop('checked');

                    // Check/uncheck all items under the head
                    $(`.item-checkbox[data-head-id="${headId}"]`).prop('checked', isChecked);
                });

                // When an item checkbox is clicked
                $(document).off('change', '.item-checkbox').on('change', '.item-checkbox', function () {
                    let headId = $(this).data('head-id');

                    // If any item is unchecked, uncheck the head checkbox
                    if ($(`.item-checkbox[data-head-id="${headId}"]:not(:checked)`).length > 0) {
                        $(`.head-checkbox[data-head-id="${headId}"]`).prop('checked', false);
                    }
                    // If all items under a head are checked, check the head checkbox
                    else {
                        $(`.head-checkbox[data-head-id="${headId}"]`).prop('checked', true);
                    }
                });
            }
            // Newly Added Part
        })
    </script>
@endpush

