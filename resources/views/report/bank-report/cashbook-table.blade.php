<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Cashbook</h4>
    </div>

    @php
        $totalCredit = 0;
        $totalDebit = 0;
        $incBalance = $openingBalance;
    @endphp

    <div class="card-body">
        <table class="w-100 table" id="account_book_table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Particulars</th>
                <th>Lot Name</th>
                <th>Index</th>
                <th>Cr.</th>
                <th>Dr.</th>
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
                <td>{{$openingBalance}}</td>
                <td></td>
                <td>{{$incBalance}}</td>
            </tr>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{$transaction->date}}</td>
                    @if($transaction->lot_item_id)
                        <td>T/E Payment by BEFTN</td>
                    @else
                        <td>{{$transaction->description}}</td>
                    @endif
                    <td>{{$transaction->lot_name}}</td>
                    <td>{{$transaction->index}}</td>
                    @if($transaction->account_type == 'credit')
                        @php
                            $totalCredit += $transaction->amount;
                            $incBalance += $transaction->amount
                        @endphp
                        <td>{{$transaction->amount}}</td>
                        <td>-</td>
                    @else
                        @php
                            $totalDebit += $transaction->amount;
                            $incBalance -= $transaction->amount
                        @endphp
                        <td>-</td>
                        <td>{{$transaction->amount}}</td>
                    @endif
                    <td>{{$incBalance}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Total</td>
                    <td>{{$totalCredit}}</td>
                    <td>{{$totalDebit}}</td>
                    <td>{{$incBalance}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
