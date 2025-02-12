<x-bootstrap-modal title="Post Transaction" hideFooter="true">

    {!! Form::open(['url' => route('cheques.post-transaction', $cheque->id), 'id' => 'cheque_transaction_form']) !!}
    <div class="row">

        <div class="col-12">
            {!! Form::label('transaction_date', 'Transaction Date', ['class' => 'control-label']) !!}
            {!! Form::text('transaction_date', '', ['class' => 'form-control date-time-picker']) !!}
        </div>

        {{--<div class="col-12">
            {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
            {!! Form::textarea('description', '', ['rows' => 4, 'class' => 'form-control']) !!}
        </div>--}}

        <hr/>

        <div class="col-12 mt-2">
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>

    </div>

</x-bootstrap-modal>
