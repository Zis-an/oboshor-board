<div>
    {!! Form::open(['url' => route('accounts.post-service-charge'), 'id' => 'addServiceCharge', 'files' => true]) !!}
    <div class="row">

        <div class="col-4">

            @if($account)
                <div class="form-group">
                    <input type="hidden" name="account_id" value="{{$account->id}}">
                    {!! Form::label('acc', 'Account*', ['class' => 'control-label']) !!}
                    {!! Form::text('acc', $account->account_no, ['class' => 'form-control', 'disabled' => true]) !!}
                </div>
            @else
                <div class="form-group">
                    {!! Form::label('account_id', 'Withdraw From*', ['class' => 'control-label'])  !!}
                    {!! Form::select('account_id', $accounts, $accountId ?? '' , ['class' => 'form-control select2', 'placeholder' => 'Select Account']) !!}
                </div>
            @endif
        </div>

        <div class="col-6">
            <div class="form-group">
                {!! Form::label('amount', 'Service Charge') !!}
                {!! Form::text('amount', '', ['class' => 'form-control', 'required' => true]) !!}
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                {!! Form::label('date', 'Transaction Date') !!}
                {!! Form::text('date', '', ['class' => 'form-control date-time-picker', 'required' => true]) !!}
            </div>
        </div>

        {{--upload file--}}

        <div class="col-6">
            <div class="form-group">
                {!! Form::label('file', 'Upload File') !!}
                {!! Form::file('file', ['class' => 'form-control', 'accept' => 'image/jpeg, image/png, application/pdf']) !!}
            </div>
        </div>

        <div class="col-12">
            {!! Form::label('description', 'Narration', ['class' => 'control-label']) !!}
            {!! Form::textarea('description', '',['rows' => 4, 'class' => 'form-control']) !!}
        </div>

        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary">Submit</button>
        </div>

    </div>
    {!! Form::close() !!}
</div>
