<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h3>{{$head->name ?? ''}}</h3>
            <p>{{$subHead->name ?? ''}}</p>
        </div>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    @php
        $initialBudget = $budgetAmount;
        $decBalance = $budgetAmount;
        $totalDebit = 0;
        $totalCredit = 0;
        $sln = 1;
    @endphp

    <div class="card-body">
        <table class="w-100 table table-bordered" id="account_book_table">
            <thead>
            <tr>
                <th>SL</th>
                <th>Tr. Date</th>
                <th>Particulars</th>
                <th>Voucher No</th>
                <th>File</th>
                <th>Cheque No.</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{$sln}}</td>
                <td>{{$financialYear->name}}</td>
                <td>Budget</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">{{number_format($initialBudget,2)}}</td>
            </tr>
            @php $i=0 @endphp
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{++$sln}}</td>
                    <td>{{date('d/m/Y', strtotime($transaction->date))}}</td>
                    <td>{{$transaction->description ?? ''}}</td>

                    <td>{{$transaction->voucher_no}}</td>

                    <td>{{$transaction->file_no}}</td>

                    <td>{{$transaction->cheque_number ?? $transaction->method}}</td>

                    @if($transaction->account_type == 'credit')
                        @php
                            $decBalance += $transaction->amount;
                            $totalCredit += $transaction->amount;
                        @endphp
                        <td class="text-right">-</td>
                        <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                    @else
                        @php
                            $decBalance -= $transaction->amount;
                            $totalDebit += $transaction->amount;
                        @endphp
                        <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                        <td class="text-right">-</td>
                    @endif
                    <td class="text-right">{{number_format($decBalance, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5" style="font-weight: bold; text-align: right;">Total:</td>
                <td></td>
                <td style="font-weight: bold; text-align: right;">{{number_format( $totalDebit,2 )}}</td>
                <td style="font-weight: bold; text-align: right;">{{number_format($totalCredit, 2)}}</td>
                <td style="font-weight: bold; text-align: right;">{{ number_format($decBalance,2)}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
