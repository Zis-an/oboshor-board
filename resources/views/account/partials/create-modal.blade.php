{!! Form::open(['url' => route('accounts.store'), 'id' => 'createAccountForm', 'files' => true]) !!}
@component('components.bootstrap-modal', ['title' => 'Add Account', 'size' => 'lg', 'submitButton' => 'Submit'])

    <div class="row">
        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('name', 'Account Name*') !!}
            {!! Form::text('name', '', ['class'=>'form-control', 'placeholder' => 'Account Name']) !!}
        </div>

        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('account_no', 'Account Number*') !!}
            {!! Form::text('account_no', '', ['class'=>'form-control', 'placeholder' => 'Account Number']) !!}
        </div>

        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('balance', 'Opening Balance*') !!}
            {!! Form::number('balance', '', ['class'=>'form-control', 'placeholder' => 'Opening Balance']) !!}
        </div>

        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('date', 'Account Date*') !!}
            {!! Form::text('date', '', ['class'=>'form-control', 'id' => 'dateCreateAccountForm']) !!}
        </div>

        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('bank_id', 'Select Bank*') !!}
            {!! Form::select('bank_id', $banks, '', ['class'=>'form-control select2', 'placeholder' => 'Select Bank', 'id' => 'selectBank']) !!}
        </div>

        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('branch_id', 'Select Branch*') !!}
            {!! Form::select('branch_id', [], '', ['class'=>'form-control', 'placeholder' => 'Select branch', 'id' => 'selectBranch']) !!}
        </div>

        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('type', 'Account Type*') !!}
            {!! Form::select('type', $accountTypes, '', ['class'=>'form-control select-account-type', 'placeholder' => 'Select Account Types']) !!}
        </div>

        <!-- Todo Show Hide these two field based on account types-->

        <div class="col-12 col-sm-6 my-2">
            {!! Form::label('interest_rate', 'Interest Rate (Yearly in Percent)*') !!}
            {!! Form::number('interest_rate', '', ['class'=>'form-control', 'placeholder' => 'interest rate']) !!}
        </div>

        <div class="col-12 col-sm-6 my-2 maturity-period">
            {!! Form::label('maturity_period', 'Maturity Period (Months) *') !!}
            {!! Form::number('maturity_period', '', ['class'=>'form-control', 'placeholder' => 'Maturity Period']) !!}
        </div>
    </div>
@endcomponent
{!! Form::close() !!}
