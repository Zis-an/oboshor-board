@extends('layouts.app')

@section('main')

    @include('partials.error-alert', ['errors' => $errors])

    <h4>Issue Item</h4>

    <div class="card">
        <div class="card-body">

            <div>
                <h4>Title: {{$inventoryRequest->title}}</h4>
                <h4>Sent By: {{$inventoryRequest->user->name ?? ''}}</h4>
            </div>

            {!! Form::open(['url' => route('issue-inventory-items.store')]) !!}

            <div class="row">

                <div class="col-12">
                    <input type="hidden" name="user_id" value="{{$inventoryRequest->user_id}}"/>
                </div>

                <div class="col-12">
                    <table class="table table-bordered" id="issue_items_table">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Request Qty</th>
                            <th>Quantity</th>
                            <th>Priority</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($inventoryRequest->items as $index=>$item)
                            @include('item.item-request.issue-request-item-row',  ['item'=>$item, 'index' => $index])
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-12">
                    <button>Submit</button>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

@endsection
