<div class="card">

    <div class="card-header">
        <h4 class="card-title">Lot Payment</h4>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="lot_table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Lot No.</th>
                <th>Index</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $datum)
                <tr>
                    <td>{{$datum->date}}</td>
                    <td>{{$datum->account->account_no ?? '-'}}</td>
                    <td>{{$datum->lot_number}}</td>
                    <td>{{$datum->index}}</td>
                    <td>{{$datum->amount}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Cashbook</h4>
    </div>
    <div class="card-body">
        <table class="w-100 table" id="account_book_table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Narration</th>
                <th>Type</th>
                <th>Method</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td>Opening Balance</td>
                <td></td>
                <td></td>
                <td></td>
                <td>Credit</td>
                <td>-</td>
            </tr>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{$transaction->date}}</td>
                    <td>{{$transaction->description}}</td>
                    <td>{{$transaction->type}}</td>
                    <td>{{$transaction->method}}</td>
                    @if($transaction->account_type == 'credit')
                        <td>{{$transaction->amount}}</td>
                        <td></td>
                    @else
                        <td></td>
                        <td>{{$transaction->amount}}</td>
                    @endif
                    <td>Amount</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Lot Return</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Date</th>
                <th>Comment</th>
                <th>Lot</th>
                <th>Index</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lotReturns as $lotReturn)
                <tr>
                    <td>{{$lotReturn->date}}</td>
                    <td>{{$lotReturn->comment}}</td>
                    <td>{{$lotReturn->lot_name}}</td>
                    <td>{{$lotReturn->index}}</td>
                    <td>{{$lotReturn->amount}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>


