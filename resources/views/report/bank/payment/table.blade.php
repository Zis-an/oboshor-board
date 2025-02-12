<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Payment Items</h3>
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
                @if(in_array($pross->index, $returnItems))
                @php continue; @endphp
                @endif
                @php 
                $count++;
                $amount_val = $amount_val + $pross->amount;
                @endphp

                <tr>
                    <td>{{date('d/m/Y', strtotime($pross->paymDate))}}</td>
                    <td>{{$pross->lot_name}}</td>
                    <td style="text-align: center;">{{$pross->index}}</td>
                    <td style="text-align: right;">{{number_format($pross->amount, 2)}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="font-weight: bold; text-align: right;">Total:</td>
                    <td style="font-weight: bold; text-align: center;">{{$count}}</td>
                    <td style="font-weight: bold; text-align: right;">{{number_format($amount_val, 2)}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
