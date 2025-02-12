{!! Form::open(['url' => route('withdraws.store'), 'id' => 'createWithdrawForm', 'files' => true]) !!}
<div class="row">
    <div class="col-4">

        @if($account)
            <div class="form-group">
                <input type="hidden" name="account_id" value="{{$account->id}}">
                {!! Form::label('acc', 'Withdraw From*', ['class' => 'control-label']) !!}
                {!! Form::text('acc', $account->account_no, ['class' => 'form-control', 'disabled' => true]) !!}
            </div>
        @else
            <div class="form-group">
                {!! Form::label('account_id', 'Withdraw From*', ['class' => 'control-label'])  !!}
                {!! Form::select('account_id', $accounts, $accountId ?? '' , ['class' => 'form-control select2', 'placeholder' => 'Select Account']) !!}
            </div>
        @endif
    </div>
    <div class="col-4">
        <div class="form-group">
            {!! Form::label('amount', 'Amount*', ['class' => 'control-label'])  !!}
            {!! Form::text('amount', '', ['class' => 'form-control', 'placeholder' => 'Amount']) !!}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <div class="form-group">
                {!! Form::label('date', 'Date*', ['class' => 'control-label'])  !!}
                {!! Form::date('date', '', ['class' => 'form-control', 'placeholder' => 'Date', 'id' => 'withdrawDate']) !!}
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            {!! Form::label('file', 'Upload File') !!}
            {!! Form::file('file', ['class' => 'form-control', 'accept' => 'image/jpeg, image/png, application/pdf']) !!}
        </div>
    </div>

    <div class="col-12 col-sm-6">
        <div class="form-group">
            <div class="form-group">
                {{Form::label('transaction_method', 'Select Transaction Method')}}
                {{Form::select('transaction_method', ['cheque' => 'Cheque'], '', ['class' => 'form-control'])}}
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('cheque_date', 'Cheque Date*') !!}
                    {!! Form::date('cheque_date', '', ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('cheque_number', 'Cheque Number*') !!}
                    {!! Form::text('cheque_number', '', ['class'=>'form-control', 'placeholder' => 'Cheque Number*']) !!}
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('cheque_file', 'Upload Cheque Document/Image') !!}
                    {!! Form::file('cheque_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                </div>
            </div>

        </div>
    </div>

    {{--<div class="col-12 d-none transaction-beftn">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('bank', 'Bank*') !!}
                    {!! Form::text('bank', '', ['class'=>'form-control', 'placeholder' => 'Bank*']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('beftn_transaction_id', 'Transaction Id') !!}
                    {!! Form::text('beftn_transaction_id', '', ['class'=>'form-control', 'placeholder' => 'Transaction Id']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 d-none transaction-pay-order">
        <div class="row">

            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('cheque_date', 'Pay Order Date*') !!}
                    {!! Form::date('cheque_date', '', ['class'=>'form-control', 'placeholder' => 'Date*']) !!}
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('pay_order_number', 'Pay Order Number*') !!}
                    {!! Form::text('pay_order_number', '', ['class'=>'form-control', 'placeholder' => 'Pay Order Number*']) !!}
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('bank', 'Bank') !!}
                    {!! Form::text('bank', '', ['class'=>'form-control', 'placeholder' => 'Bank']) !!}
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="form-group">
                    {!! Form::label('pay_order_file', 'Upload Pay Order Document/Image') !!}
                    {!! Form::file('pay_order_file', ['class'=>'form-control', 'accept' => 'image/jpeg, image/png, application/pdf', 'placeholder' => 'Image/Pdf*']) !!}
                </div>
            </div>

        </div>
    </div>--}}


    <div class="col-12">
        {{Form::label('description', 'Narration/Income Source*')}}
        {{Form::text('description', '', ['class' => 'form-control', 'placeholder' => 'Description', 'rows' => 2])}}
    </div>

    <div class="mt-3">
        <button class="btn btn-primary float-right">Save</button>
    </div>

</div>
{!! Form::close() !!}
