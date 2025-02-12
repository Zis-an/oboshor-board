<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Income Report</h3>
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="income_table">
            <thead>
            <tr>
                <th>SL.</th>
                <th>Date</th>
                <th>Head Item</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($incomes as $index=>$income)
                <tr>
                    <td>{{$index}}</td>
                    <td>{{Carbon\Carbon::parse($income->date)->format('d/m/Y')}}</td>
                    <td>{{$income->item_name ?? ''}}</td>
                    <td>{{$income->description}}</td>
                    <td>{{number_format($income->amount, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td>{{number_format($total, 2)}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
