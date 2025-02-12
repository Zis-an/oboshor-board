@component('components.bootstrap-modal', ['title' => 'Update Bank Account', 'size' => 'lg', 'hideFooter' => true])
    {!! Form::open(['url' => route('accounts.update', $account->id), 'id' => 'updateAccountForm', 'method' => 'PUT', 'files' => true]) !!}
    <div class="row">
        <div class="col-12 col-sm-6">
            {!! Form::label('name', 'Account Name*') !!}
            {!! Form::text('name', $account->name, ['class'=>'form-control']) !!}
        </div>

        <div class="col-12 col-sm-6">
            {!! Form::label('account_no', 'Account Number*') !!}
            {!! Form::text('account_no', $account->account_no, ['class'=>'form-control']) !!}
        </div>

        <div class="col-12 col-sm-6">
            {!! Form::label('bank_id', 'Select Bank*') !!}
            {!! Form::select('bank_id', $banks, $account->bank_id, ['class'=>'form-control', 'placeholder' => 'Select Bank', 'id' => 'selectBank']) !!}
        </div>

        <div class="col-12 col-sm-6">
            {!! Form::label('branch_id', 'Select Branch*') !!}
            {!! Form::select('branch_id', $branches, $account->branch_id, ['class'=>'form-control', 'placeholder' => 'Select branch', 'id' => 'selectBranch']) !!}
        </div>

        <div class="col-12 col-sm-6">
            {!! Form::label('account_type_id', 'Account Type*') !!}
            {!! Form::select('account_type_id', $accountTypes, $account->type, ['class'=>'form-control select-account-type', 'placeholder' => 'Select Account Types']) !!}
        </div>

        <div class="col-12 col-sm-6">
            {!! Form::label('interest_rate', 'Interest Rate*') !!}
            {!! Form::number('interest_rate', $account->interest_rate, ['class'=>'form-control', 'placeholder' => 'interest rate']) !!}
        </div>

        <div class="col-12 col-sm-6 maturity-period {{$account->type === 'STD' ? 'd-none': ''}}">
            {!! Form::label('maturity_period', 'Maturity Period (Months) *') !!}
            {!! Form::number('maturity_period', $account->maturity_period, ['class'=>'form-control', 'placeholder' => 'Maturity Period']) !!}
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">Update</button>
        </div>
    </div>

    {!! Form::close() !!}
@endcomponent

