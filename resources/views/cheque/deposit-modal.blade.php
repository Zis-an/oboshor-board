<x-bootstrap-modal title="Deposit Cheque" hideFooter="true">

    {!! Form::open(['url' => route('cheques.post-deposit', $cheque->id), 'id' => 'cheque_deposit_form']) !!}
    <div class="row">

        <div class="col-12">
            {!! Form::label('account_id', 'Select Account', ['class' => 'control-label']) !!}
            {!! Form::select('account_id', $accounts, '', ['class' => 'form-control', 'placeholder' => 'Select Account']) !!}
        </div>

        <div class="col-12">
            {!! Form::label('deposited_date', 'Deposited Date', ['class' => 'control-label']) !!}
            {!! Form::text('deposited_date', '', ['class' => 'form-control date-time-picker']) !!}
        </div>

        <div class="col-12 mb-2">
            {!! Form::label("head_id", 'Income Head *') !!}
            {!! Form::select("head_id", $incomeHeads, '', ['class' => 'form-control','placeholder' => 'Select Income Head']) !!}
        </div>

        <div class="col-sm-12">
            {!! Form::label("head_item_id", 'Income Sub Head') !!}
            {!! Form::select("head_item_id", [], '', ['class' => 'form-control','placeholder' => 'Select Income Head']) !!}
        </div>

        <div class="col-12">
            {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
            {!! Form::textarea('description', '', ['rows' => 4, 'class' => 'form-control']) !!}
        </div>

        <div class="col-12 my-2">
            <label>
                <input type="checkbox" name="transaction_completed" id="transaction_checkbox"
                       value="1"
                />
                Is transaction completed
            </label>
        </div>

        <div class="col-12 d-none" id="transaction_date">
            <div class="row">

                <div class="col-12">
                    {!! Form::label('transaction_date', 'Transaction Date', ['class' => 'control-label']) !!}
                    {!! Form::text('transaction_date', '', ['class' => 'form-control date-time-picker']) !!}
                </div>

            </div>
        </div>

        <hr/>

        <div class="col-12 mt-2">
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>

    </div>

</x-bootstrap-modal>
