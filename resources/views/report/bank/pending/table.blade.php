<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Pending Items</h3>
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
                @php 
                $count = 0; 
                $amount_val = 0.00;
                @endphp
                @foreach($prossItems as $pross)
                @php 
                $count++;
                $amount_val = $amount_val + $pross->amount;
                @endphp

                <tr>
                    <td>{{$pross->date}}</td>
                    <td>{{$pross->short_name}}</td>
                    <td style="text-align: center;">{{$pross->index}}</td>
                    <td style="text-align: right;">{{number_format($pross->amount, 2)}}</td>
                </tr>
                @endforeach
                
                @foreach($transac as $val)
                @php 
                $count++;
                $amount_val = $amount_val + $val->amount;
                @endphp

                <tr>
                    <td>{{$val->date}}</td>
                    <td>{{$val->short_name}}</td>
                    <td style="text-align: center;">{{$val->index}}</td>
                    <td style="text-align: right;">{{number_format($val->amount, 2)}}</td>
                </tr>
                @endforeach

                @foreach($items as $item)

                @php
                $trans = \App\Models\Transaction::where('lot_item_id', $item->indexId)->orderBy('date', 'DESC')->first();
                @endphp

                @if($trans->account_type == 'credit' && $trans->date <= $end)

                @php 
                $count++;
                $amount_val = $amount_val + $item->amount;
                @endphp

<!--                <tr>
                    <td>{{$item->date}}</td>
                    <td>{{$item->short_name}}</td>
                    <td style="text-align: center;">{{$item->index}}</td>
                    <td style="text-align: right;">{{number_format($item->amount, 2)}}</td>
                </tr>-->
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="font-weight: bold; text-align: right;">Total:</td>
                    <td style="font-weight: bold; text-align: center;">{{count($prossItems) + count($transac)}}</td>
                    <td style="font-weight: bold; text-align: right;">{{number_format(($prossItems->sum('amount') + $transac->sum('amount')), 2)}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
