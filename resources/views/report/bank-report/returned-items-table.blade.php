<div class="card">

    <div class="card-header">
        <h4 class="card-title">Returned Items</h4>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="lot_hold_table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Lot Name</th>
                <th>Index</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{$item->date}}</td>
                    <td>{{$item->account->account_no ?? '-'}}</td>
                    <td>{{$item->lot_name}}</td>
                    <td>{{$item->index}}</td>
                    <td>{{$item->amount}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
