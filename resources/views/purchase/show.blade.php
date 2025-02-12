@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h4>View Purchase</h4>
            <a href="{{route('purchases.edit', $purchase->id)}}" class="btn btn-primary">Edit</a>
        </div>
    </section>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered w-100">
                <tr>
                    <th>
                        Date:
                    </th>
                    <td>
                        {{Carbon\Carbon::parse($purchase->date)->format('d-m-Y h:i A')}}
                    </td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>{{number_format($purchase->amount, 2)}}</td>
                </tr>
                <tr>
                    <th>Vat</th>
                    <td>{{number_format($purchase->vat, 2)}}</td>
                </tr>
                <tr>
                    <th>Tax</th>
                    <td>{{number_format($purchase->tax, 2)}}</td>
                </tr>
                <tr>
                    <th>Payable Amount</th>
                    <td>{{number_format($purchase->amount - ($purchase->vat + $purchase->tax))}}</td>
                </tr>
            </table>

            <h5 class="my-2">Purchased Items</h5>

            <table class="table mt-2">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($purchase->purchaseItems as $item)
                    <tr>
                        <td>{{$item->item->name}}</td>
                        <td>{{$item->quantity}}</td>
                        <td>{{$item->unit}}</td>
                        <td>{{$item->amount}}</td>
                    </tr>
                @endforeach
                <tr></tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
