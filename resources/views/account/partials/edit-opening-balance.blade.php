@component('components.bootstrap-modal', ['title' => 'View Account', 'submitButton' => 'Submit', 'hideFooter' => true])
    {!! Form::open(['url' => route('accounts.update-opening-balance', $account->id), 'id' => 'updateOpeningBalanceForm']) !!}
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {!! Form::label('amount', 'Opening Balance*', ['class' => 'control-label']) !!}
                {!! Form::text('amount', $openingBalance->amount ?? '', ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                {!! Form::label('date', 'Opening Balance Date*', ['class' => 'control-label']) !!}
                {!! Form::datetimelocal('date', $openingBalance->date ?? '' , ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary">Update Opening Balance</button>
        </div>

    </div>
    {!! Form::close() !!}
@endcomponent
