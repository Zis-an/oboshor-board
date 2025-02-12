<div class="modal-dialog">
    {!! Form::open(['url' => '/lots/'.$lot->id.'/pay',
    'method' => 'POST', 'id' => 'createLotPayment']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5">Make Payment For All</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div>Total Payable Amount: {{number_format($totalAmount, 2)}}</div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('bank', 'Name*', ['class' => 'control-label']) !!}
                        {!! Form::select('bank', $banks, '', ['class'=>'form-control', 'id' => 'lotBankSelect', 'placeholder' => 'Select Bank']) !!}
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('account', 'Select Bank Account') !!}
                        {!! Form::select('account', [], '', ['class'=>'form-control', 'id' => 'lotSelectAccount', 'placeholder' => 'Select Account']) !!}
                    </div>
                </div>
            </div>

            <div id="accountBalance"></div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
