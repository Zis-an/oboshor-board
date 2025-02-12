<div class="modal-dialog">
    {!! Form::open(['url' => route('reconciliations.update', $reconciliation->id),
    'method' => 'PUT', 'id' => 'updateReconciliationForm']) !!}
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
                    <div class="form-group">
                        {!! Form::label('account', 'Account*', ['class' => 'control-label']) !!}
                        {!! Form::select('account', $accounts, $reconciliation->account_id, ['class'=>'form-control', 'disabled' => true]) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('type', 'Type*') !!}
                        {!! Form::select('type', $types, $reconciliation->account_type, ['class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('amount', 'Account*', ['class' => 'control-label']) !!}
                        {!! Form::number('amount', $reconciliation->amount, ['class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('date', 'Date*', ['class' => 'control-label']) !!}
                        {!! Form::date('date', $reconciliation->date, ['class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('note', 'Note*', ['class' => 'control-label']) !!}
                        {!! Form::textarea('note', $reconciliation->description, ['class'=>'form-control', 'rows' =>2]) !!}
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

