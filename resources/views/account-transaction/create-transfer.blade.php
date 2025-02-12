{!! Form::open(['url' => route('transfers.store'), 'id' => 'createTransferForm']) !!}
@component('components.bootstrap-modal', ['title' => 'Transfer Money', 'submitButton' => 'submit'])
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {!! Form::label('account_id', 'Transfer From*', ['class' => 'control-label'])  !!}
                {!! Form::select('account_id', $accounts, $accountId ?? '' , ['class' => 'form-control select2', 'placeholder' => 'Select Account']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('account_to', 'Transfer To*', ['class' => 'control-label'])  !!}
                {!! Form::select('account_to', $accounts, '' , ['class' => 'form-control select2', 'placeholder' => 'Select Account']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('amount', 'Amount*', ['class' => 'control-label'])  !!}
                {!! Form::text('amount', '', ['class' => 'form-control', 'placeholder' => 'Amount']) !!}
            </div>
            <div class="form-group">
                <div class="form-group">
                    {!! Form::label('date', 'Date*', ['class' => 'control-label'])  !!}
                    {!! Form::text('date', '', ['class' => 'form-control', 'placeholder' => 'Date', 'id' => 'transferDate']) !!}
                </div>
            </div>
        </div>
    </div>
@endcomponent

{!! Form::close() !!}
