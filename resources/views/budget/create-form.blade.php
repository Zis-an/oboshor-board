{{--Newly Added Part--}}
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
{{--Newly Added Part--}}

<div class="card-body" id="budget_container">

    @php
        $totalAmount = 0;
    @endphp

    <div>
{{--        <table class="table table-bordered">--}}
{{--            <thead>--}}
{{--            <tr>--}}
{{--                <th>Name</th>--}}
{{--                @if(!empty($prevFinancialYear))--}}
{{--                    <th style="width: 200px">Budget of {{$prevFinancialYear->name ?? ''}}</th>--}}
{{--                    <th>Actual {{$type}} {{$prevFinancialYear->name}}</th>--}}
{{--                @endif--}}
{{--                <th>Amount</th>--}}
{{--            </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--            @foreach($heads as $headIndex=>$head)--}}
{{--                <tr class="sub-total-row">--}}
{{--                    <input type="hidden"--}}
{{--                           name="{{'items['. $headIndex . '][head_id]'}}"--}}
{{--                           value="{{$head->id}}">--}}
{{--                    <td class="text-bold" style="width: 75%">{{$head->name}}</td>--}}
{{--                    @if($type == 'expense' && !empty($prevFinancialYear))--}}
{{--                        <td class="text-right font-weight-bold">--}}
{{--                            {{number_format($head->budget->amount ?? 0, 2)}}--}}
{{--                        </td>--}}
{{--                        @php--}}
{{--                            $headAmount = $head->transactionItems->sum('amount') ?? 0;--}}
{{--                            $totalAmount += $headAmount;--}}
{{--                        @endphp--}}
{{--                        <td class="text-right font-weight-bold"--}}
{{--                            style="width: 200px">{{number_format($headAmount, 2)}}</td>--}}
{{--                    @endif--}}

{{--                    @if($type == 'income' && !empty($prevFinancialYear))--}}
{{--                        @php--}}
{{--                            $headAmount = $head->transactions->sum('amount') ?? 0;--}}
{{--                            $totalAmount += $headAmount;--}}
{{--                        @endphp--}}
{{--                        <td class="text-right font-weight-bold">--}}
{{--                            {{number_format($head->budget->amount ?? 0, 2)}}--}}
{{--                        </td>--}}
{{--                        <td class="text-right font-weight-bold"--}}
{{--                            style="width: 200px">{{number_format($headAmount, 2)}}</td>--}}
{{--                    @endif--}}

{{--                    <td style="width: 25%">--}}
{{--                        @if(isset($head->items ) && count($head->items))--}}
{{--                            {{Form::number('items['.$headIndex. '][amount]', '', ['class' => 'form-control sub-total', 'readonly' => true])}}--}}
{{--                        @else--}}
{{--                            {{Form::text('items['.$headIndex. '][amount]', '', ['class' => 'form-control sub-total', ])}}--}}
{{--                        @endif--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--                @if(isset($head->items))--}}
{{--                    @foreach( $head->items as $index=>$item)--}}
{{--                        <tr>--}}
{{--                            <input type="hidden"--}}
{{--                                   name="{{'items['. $headIndex . '][child][' . $index.'][head_item_id]'}}"--}}
{{--                                   value="{{$item->id}}">--}}
{{--                            <td style="width: 75%">{{$item->name}}</td>--}}
{{--                            @if($type == 'expense' && !empty($prevFinancialYear))--}}
{{--                                <td class="text-right"> {{number_format($item->budget->amount ?? 0, 2)}}</td>--}}
{{--                                <td class="text-right">{{number_format($item->transactionItems->sum('amount'), 2)}}</td>--}}
{{--                            @endif--}}

{{--                            @if($type == 'income' && !empty($prevFinancialYear))--}}
{{--                                <td class="text-right">--}}
{{--                                    {{number_format($item->budget->amount ?? 0, 2)}}--}}
{{--                                </td>--}}
{{--                                <td class="text-right"--}}
{{--                                    style="width: 200px">{{number_format($item->transactions->sum('amount'), 2)}}</td>--}}
{{--                            @endif--}}

{{--                            <td style="width: 25%">--}}
{{--                                {{Form::text('items['.$headIndex. '][child]['  .$index.'][amount]', '', ['class' => 'form-control td-amount'])}}--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--            </tbody>--}}
{{--            <tfoot>--}}
{{--            <tr>--}}
{{--                <td style="width:75%">Total Amount</td>--}}
{{--                <td>{{number_format($prevBudget->amount, 2)}}</td>--}}
{{--                <td>{{number_format($totalAmount, 2)}}</td>--}}
{{--                <td>--}}
{{--                    {!! Form::number('amount', 0, ['class' => 'form-control', 'id' => 'total-amount', 'readonly']) !!}--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            </tfoot>--}}
{{--        </table>--}}
    </div>

{{--    <div class="d-flex justify-content-end mt-2">--}}
{{--        <button type="submit" class="btn btn-primary">Save</button>--}}
{{--    </div>--}}
</div>




{{--Newly Added Part--}}
<div class="modal fade" id="headsModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel">Select Heads & Items</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul id="headsList" class="list-group"></ul> <!-- Populated dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="applySelection">Apply</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{--Newly Added Part--}}

{{--Newly Added Part--}}
<script>
    let prevFinancialYear = @json($prevFinancialYear);
    let type = @json($type);
    let headsData = @json($heads);
    let totalPrevBudgetAmount = 0; // Track total previous budget amount
    let totalHeadAmount = 0; // Track total actual amount


    $(document).on('click', '#applySelection', function () {
        let selectedHeads = [];
        let selectedItems = [];

        $('.head-checkbox:checked').each(function () {
            let headId = $(this).data('head-id');
            let headName = $(this).closest('.list-group-item').find('strong').text(); // Get head name
            selectedHeads.push({ id: headId, name: headName });
        });

        $('.item-checkbox:checked').each(function () {
            let itemId = parseInt($(this).data('item-id')); // ✅ Convert to number
            let headId = parseInt($(this).data('head-id')); // ✅ Convert to number
            let itemName = $(this).closest('.list-group-item').find('span').text();
            selectedItems.push({ id: itemId, headId: headId, name: itemName });
        });

        // Building the table structure
        let tableHtml = `<table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Name</th>`;

        if (prevFinancialYear) {
            tableHtml += `
                <th style="width: 200px">Budget of ${prevFinancialYear.name ?? ''}</th>
                <th>Actual ${type} ${prevFinancialYear.name}</th>`;
        }

        tableHtml += `<th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>`;

        selectedHeads.forEach((head, headIndex) => {
            // Ensure that headData is assigned correctly
            let headData = headsData.find(h => h.id === head.id);

            // Check if headData is found, if not, skip this head
            if (!headData) {
                console.error("Head data not found for id:", head.id); // Log the missing data for debugging
                return; // Skip the current iteration if headData is not found
            }

            // Now, use headData safely inside the loop
            let prevBudgetAmount = parseFloat(headData?.budget?.amount ?? 0); // Ensure the amount is a number
            let headAmount = headData?.transaction_items?.reduce((sum, txn) => sum + parseFloat(txn.amount), 0) ?? 0; // Calculate headAmount

            totalPrevBudgetAmount += prevBudgetAmount; // Accumulate total previous budget
            totalHeadAmount += headAmount; // Accumulate total actual amount

            tableHtml += `
                <tr class="sub-total-row">
                    <input type="hidden" name="items[${headIndex}][head_id]" value="${head.id}">
                    <td class="text-bold"><strong>${head.name}</strong></td>`;

            if (prevFinancialYear) {
                tableHtml += `
                    <td class="text-right font-weight-bold">${prevBudgetAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="text-right font-weight-bold">${headAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
            }

            tableHtml += `<td></td></tr>`;

            let itemsUnderHead = selectedItems.filter(item => item.headId === head.id);
            itemsUnderHead.forEach(item => {
                let itemData = headData?.items?.find(i => i.id === item.id);

                // Ensure itemData is found
                if (!itemData) {
                    console.error("Item data not found for item id:", item.id); // Log missing item data
                    return; // Skip this item if data is missing
                }

                let itemPrevBudgetAmount = parseFloat(itemData?.budget?.amount ?? 0);
                let itemActualExpense = itemData?.transactions?.reduce((sum, txn) => sum + txn.amount, 0) ?? 0;
                let itemTransactionAmount = itemData?.transaction_items?.reduce((sum, txn) => sum + parseFloat(txn.amount), 0) ?? 0; // Calculate transaction sum for item

                tableHtml += `
                    <tr>
                        <td class="pl-4"><em>${item.name}</em></td>`;

                if (prevFinancialYear) {
                    tableHtml += `
                        <td class="text-right">${itemPrevBudgetAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-right">${itemTransactionAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                }

                tableHtml += `
                    <td>
                        <input type="hidden" name="items[${headIndex}][child][${item.id}][head_item_id]" value="${item.id}">
                        <input type="number" class="form-control sub-total" name="items[${headIndex}][child][${item.id}][amount]" placeholder="Enter amount">
                    </td>
                </tr>`;
            });
        });

        tableHtml += `</tbody>
    <tfoot>
        <tr>
            <td class="font-weight-bold">Total Amount</td>
            <td class="text-right font-weight-bold">${totalPrevBudgetAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td class="text-right font-weight-bold">${totalHeadAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        </tr>
    </tfoot>
</table>
<div class="d-flex justify-content-end mt-2">
    <button type="submit" class="btn btn-primary">Save</button>
</div>`;

        $('#budget_container div').html(tableHtml);
        $('#budget_container').show();
        $('#headsModal').modal('hide');
    });
</script>
{{--Newly Added Part--}}
