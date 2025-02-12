@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>View Purchase Plan</h4>
        </div>
    </section>

    <section class="card">

        <div class="card-body" id="budgetContainer">
            <div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($budgetItems as $budgetIndex=>$budgetItem)

                        <tr class="sub-total-row">
                            <td class="text-bold" style="width: 75%">{{$budgetItem->headItem->name}}</td>
                            <td class="text-right font-weight-bold">{{number_format($budgetItem->amount, 2)}}</td>
                        </tr>

                        @foreach($budgetItem->items as $index=>$item)
                            <tr>
                                <td style="width: 75%">{{$item->item->name ?? ''}}</td>
                                <td class="text-right" style="width: 25%">
                                    <div>
                                        {{number_format($item->amount, 2)}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                    <tfoot style="background-color: #eee">
                    <tr>
                        <td>
                            <strong>Total</strong>
                        </td>
                        <td class="text-right">
                            <div>
                                {{number_format($budget->amount, 2)}}
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection
