@component('components.bootstrap-modal', ['title' => 'Add Service Charge', 'hideFooter' => true])
    <div>
        {!! Form::open(['url' => route('accounts.post-charge', $account->id), 'id' => 'addServiceCharge']) !!}
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    {!! Form::label('amount', 'Service Charge') !!}
                    {!! Form::text('amount', '', ['class' => 'form-control', 'required' => true]) !!}
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    {!! Form::label('date', 'Transaction Date') !!}
                    {!! Form::datetimelocal('date', '', ['class' => 'form-control', 'required' => true]) !!}
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary">Submit</button>
            </div>

        </div>
        {!! Form::close() !!}
    </div>
@endcomponent
