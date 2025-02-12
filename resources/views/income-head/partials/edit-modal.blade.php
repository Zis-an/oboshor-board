<div class="modal-dialog">
    {!! Form::open(['url' => route('income-heads.update', $incomeHead->id),
    'method' => 'PUT', 'id' => 'updateExpenseHeadForm']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5">Update</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="my-2">
                {!! Form::label('name', 'Name*') !!}
                {!! Form::text('name', $incomeHead->name, ['class'=>'form-control']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', $incomeHead->description, ['class'=>'form-control']) !!}
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

