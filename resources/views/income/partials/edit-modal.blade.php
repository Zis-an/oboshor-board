<div class="modal-dialog">
    {!! Form::open(['url' => route('incomes.update', $income->id), 'id' => 'updateIncomeForm', 'method' => 'PUT']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Update  Income</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="my-2">
                {!! Form::label('date', 'Date*') !!}
                {!! Form::text('date', $income->date, ['class'=>'form-control']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('amount', 'Amount*') !!}
                {!! Form::text('amount', $income->amount, ['class'=>'form-control']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('head_id', 'Select Income Head*') !!}
                {!! Form::select('head_id', $heads, $income->head_id, ['class'=>'form-control', 'placeholder' => 'Select Income Head']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('description', 'Description*') !!}
                {!! Form::textarea('description', $income->description, ['class'=>'form-control', 'rows' => 4]) !!}
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
