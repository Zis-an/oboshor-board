<div class="modal-dialog modal-lg">
    {!! Form::open(['url' => route('account-types.update', $accountType->id),
    'method' => 'PUT', 'id' => 'updateAccountTypeForm']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5">Update</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12">
                    {!! Form::label('name', 'Name*') !!}
                    {!! Form::text('name', $accountType->name, ['class'=>'form-control']) !!}
                </div>
                <div class="col-6 col-sm-4">
                    <div class="form-check">
                        <label>
                            {!! Form::checkbox('allow_withdraw', '1', $accountType->allow_withdraw, ['class'=> 'form-check-input']) !!}
                            Allow Withdraw
                        </label>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="form-check">
                        <label>
                            {!! Form::checkbox('allow_deposit', '1', $accountType->allow_deposit, ['class'=> 'form-check-input']) !!}
                            Allow Deposit
                        </label>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="form-check">
                        <label>
                            {!! Form::checkbox('has_interest', '1', $accountType->has_interest, ['class'=> 'form-check-input']) !!}
                            Has Interest
                        </label>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="form-check">
                        <label>
                            {!! Form::checkbox('has_maturity_period', '1', $accountType->has_maturity_period, ['class'=> 'form-check-input']) !!}
                            Has Maturity Period
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    {!! Form::label('description', 'Description*') !!}
                    {!! Form::textarea('description', $accountType->description, ['class'=>'form-control', 'rows' => 2]) !!}
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

