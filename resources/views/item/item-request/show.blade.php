@extends('layouts.app')

@section('main')
    <div>
        <h4>
            {{$inventoryRequest->title}}
        </h4>
        <h5>Date: {{$inventoryRequest->date}}</h5>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Priority</th>
        </tr>
        </thead>
        <tbody>
            @foreach($inventoryRequest->items as $item)
                <tr>
                    <td>{{$item->item->name ?? ''}}</td>
                    <td>{{$item->quantity ?? 0}}</td>
                    <td>{{$item->priority}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
