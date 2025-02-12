<html>
<head>
    <style>
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }

        .table-bordered, td, th {
            border: 1px solid #494d54;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

    </style>
</head>
</html>


<div>

    <div>
        <table class="table">
            <thead>
            <tr style="margin-bottom: 5px">
                <td colspan="4" class="text-center">Non Government Teacher Employee Retirement Benefit Board</td>
            </tr>
            <tr>
                <td colspan="4" class="text-center">Education Ministry</td>
            </tr>
            <tr>
                <td colspan="4" class="text-center">Hold Index Statement</td>
            </tr>
            <tr>
                <th class="text-right">Date</th>
                <th class="text-right">Lot Name</th>
                <th class="text-right">Index</th>
                <th class="text-right">Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td class="text-right">{{\Carbon\Carbon::parse($item->date)->format('d-m-Y')}}</td>
                    <td class="text-right">{{$item->lot_name}}</td>
                    <td class="text-right">{{$item->index}}</td>
                    <td class="text-right">{{number_format($item->amount, 2)}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td class="text-right">Total:</td>
                <td class="text-right">{{number_format(count($items), 2)}}</td>
                <td class="text-right">{{number_format($items->sum('amount'), 2)}}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
