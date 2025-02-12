<div class="modal-dialog">
    {!! Form::open(['url' => route('reconciliations.store'), 'id' => 'createExpenseHeadForm']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Add Reconciliation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('account', 'Account*', ['class' => 'control-label']) !!}
                        {!! Form::select('account', $accounts, '', ['class'=>'form-control', 'placeholder' => '--Select Account--']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('type', 'Type*') !!}
                        {!! Form::select('type', $types, '', ['class'=>'form-control', 'placeholder' => '--Select Type--']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('amount', 'Account*', ['class' => 'control-label']) !!}
                        {!! Form::number('amount', '', ['class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}
                        {!! Form::date('date', '', ['class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('note', 'Note*', ['class' => 'control-label']) !!}
                        {!! Form::textarea('note', '', ['class'=>'form-control', 'rows' =>2]) !!}
                    </div>
                </div>

            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
