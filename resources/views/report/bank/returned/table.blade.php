<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Returned Items</h3>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="lot_hold_table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Lot Name</th>
                <th>Index</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{date('d/m/Y', strtotime($item->tranDate))}}</td>
                    <td>{{$item->lot_short_name}}</td>
                    <td style="text-align: center;">{{$item->index}}</td>
                    <td style="text-align: right;">{{number_format($item->amount, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="font-weight: bold; text-align: right;">Total:</td>
                    <td style="font-weight: bold; text-align: center;">{{count($items)}}</td>
                    <td style="font-weight: bold; text-align: right;">{{number_format($items->sum('amount'), 2)}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
