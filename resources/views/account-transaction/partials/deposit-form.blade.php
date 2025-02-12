{!! Form::open(['url' => route('deposits.store'), 'id' => 'createDepositForm', 'files' => true]) !!}
<div class="row">
    <div class="col-12">
        @include('partials.error-alert',['errors' => $errors])
    </div>

    <input type="hidden" name="type" value="{{request()->query('type') == 'profit' ? 'profit': ''}}" />

    <div class="col-6 col-md-4">
        @if($account)
            <div class="form-group">
                <input type="hidden" name="account_id" value="{{$account->id}}">
                {!! Form::label('acc', 'Deposit To*', ['class' => 'control-label']) !!}
                {!! Form::text('acc', $account->is_cash_account ? 'Petty Cash' : $account->account_no, ['class' => 'form-control', 'disabled' => true]) !!}
            </div>
        @else
            <div class="form-group">
                {!! Form::label('account_id', 'Deposit To*', ['class' => 'control-label'])  !!}
                {!! Form::select('account_id', $accounts, $accountId ?? '' , ['class' => 'form-control select2', 'placeholder' => 'Select Account']) !!}
            </div>
        @endif
    </div>

    <div class="col-6 col-md-4">
        <div class="form-group">
            {!! Form::label('amount', 'Amount*', ['class' => 'control-label'])  !!}
            {!! Form::text('amount', '', ['class' => 'form-control', 'placeholder' => 'Amount']) !!}
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="form-group">
            <div class="form-group">
                {!! Form::label('date', 'Deposit Date*', ['class' => 'control-label'])  !!}
                {!! Form::text('date', '', ['class' => 'form-control date-time-picker', 'placeholder' => 'Date', 'id' => 'depositDate']) !!}
            </div>
        </div>
    </div>

    <div class="col-sm-6 mb-2">
        {!! Form::label("head_id", 'Income Head') !!}
        {!! Form::select("head_id", $incomeHeads, '', ['class' => 'form-control','placeholder' => 'Select Income Head']) !!}
    </div>

    <div class="col-sm-6 mb-2">
        {!! Form::label("head_item_id", 'Income Sub Head') !!}
        {!! Form::select("head_item_id", [], '', ['class' => 'form-control','placeholder' => 'Select Income Head']) !!}
    </div>

    <div class="col-sm-6">
        {!! Form::label('file', 'Upload File') !!}
        {!! Form::file('file', ['class' => 'form-control', 'accept' => 'image/jpeg, image/png, application/pdf']) !!}
    </div>

    <div class="col-12 col-sm-6">
        <div class="form-group">
            <div class="form-group">
                {{Form::label('transaction_method', 'Select Transaction Method')}}
                {{Form::select('transaction_method', $transactionMethods, '', ['class' => 'form-control', 'placeholder' => 'Select Method'])}}
            </div>
        </div>
    </div>

    <div class="col-12 d-none transaction-cheque">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('cheque_date', 'Cheque Date*') !!}
                    {!! Form::text('cheque_date', '', ['class'=>'form-control date-time-picker', 'placeholder' => 'Date*']) !!}
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('cheque_transaction_date', 'Cheque Transaction Date*') !!}
                    {!! Form::text('cheque_transaction_date', '', ['class'=>'form-control date-time-picker', 'placeholder' => 'Date*']) !!}
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

    <div class="col-12 d-none transaction-beftn">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('from_account_id', 'From Account') !!}
                    {!! Form::select('from_account_id', $accounts, '', ['class' => 'form-control', 'placeholder' => '-select-account-']) !!}
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
    </div>


    <div class="col-12">
        {{Form::label('description', 'Narration/Income Source*')}}
        {{Form::text('description', '', ['class' => 'form-control', 'placeholder' => 'Description', 'rows' => 2])}}
    </div>

    <div class="col-12 mt-4">
        <button class="btn btn-primary float-right">Save</button>
    </div>

</div>
{!! Form::close() !!}
