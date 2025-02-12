<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cashbook</h3>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    @php
        $totalCredit = $openingBalance;
        $totalDebit = 0;
        $incBalance = $openingBalance;
        $sln = 1;
    @endphp

    <div class="card-body">
        <table class="w-100 table" id="account_book_table">
            <thead>
            <tr>
                <th>SL</th>
                <th>Date</th>
                <th>Title</th>
                <th>Particulars</th>
                <th style=" width: 80px;">Lot Name</th>
                <th>Index</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{$sln}}</td>
                <td>{{date('d/m/Y', strtotime($start))}}</td>
                <td>Opening Balance</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">{{number_format($openingBalance,2)}}</td>

                <td class="text-right">{{number_format($incBalance, 2)}}</td>
            </tr>
            @php $i=0 @endphp
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{++$sln}}</td>
                    <td>{{date('d/m/Y', strtotime($transaction->date))}}</td>

                    @php
                        $description = $transaction->description;
                        $headName = $transaction->head ?? '';
                        $headItem = $transaction->headItem->name ?? '';

                        if($transaction->type == 'expense' && !empty($transaction->expenseItems)){
                            $headName = $transaction->expenseItems->pluck('head.name')->join(', ');
                            $headItem = $transaction->expenseItems->pluck('headItem.name')->join(',');
                        }

                    @endphp

                    <td>
                        @if($headItem)
                            <span class="{{is_unicode($headItem) ? 'bn-font' : ''}}">{{$headItem}}</span>
                        @elseif($headName)
                            <span class="{{is_unicode($headName) ? 'bn-font' : ''}}">{{$headName}}</span>
                        @else
                            <span>-</span>
                        @endif
                    </td>

                    {{--@if(!empty($headName) && !empty($headItem))
                        @php
                            $description = $headName . ' (' .$headItem. ')';
                        @endphp
                    @else
                        @php
                            $description = $headName ?? $headItem ?? $transaction->description;
                        @endphp
                    @endif--}}

                    <td class="{{is_unicode($description) ? 'bn-font' : ''}}">{{$description}}</td>

                    {{--<td>{{$transaction->file_page}}</td>--}}
                    <td>{{$transaction->short_name}}</td>
                    <td>
                        @if($transaction->index)
                            @php $i++ @endphp
                            {{$transaction->index}}
                        @else
                            -
                        @endif
                    </td>
                    @if($transaction->account_type == 'credit')
                        @php
                            $totalCredit += $transaction->amount;
                            $incBalance += $transaction->amount
                        @endphp
                        <td class="text-right">-</td>
                        <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                    @else
                        @php
                            $totalDebit += $transaction->amount;
                            $incBalance -= $transaction->amount
                        @endphp
                        <td class="text-right">{{number_format($transaction->amount, 2)}}</td>
                        <td class="text-right">-</td>
                    @endif
                    <td class="text-right">{{number_format($incBalance, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td>{{++$sln}}</td>
                <td style=" font-weight: bold;">{{date('d/m/Y', strtotime($end))}}</td>
                <td colspan="3" style="font-weight: bold; text-align: right;">Total:</td>
                <td style="font-weight: bold; text-align: center;">{{$i}}</td>
                <td style="font-weight: bold; text-align: right;">{{number_format( $totalDebit,2 )}}</td>
                <td style="font-weight: bold; text-align: right;">{{number_format($totalCredit, 2)}}</td>
                <td style="font-weight: bold; text-align: right;">{{ number_format($incBalance,2)}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
