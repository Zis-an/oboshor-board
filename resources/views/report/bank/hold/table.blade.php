<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Hold Items</h4>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button class="btn btn-info mx">Excel</button>
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
                    <td>{{$item->date}}</td>
                    <td>{{$item->lot_name}}</td>
                    <td>{{$item->index}}</td>
                    <td class="text-right">{{number_format($item->amount, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
