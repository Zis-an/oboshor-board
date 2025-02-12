<div class="modal-dialog">
    {!! Form::open(['url' => route('heads.update', $head->id),
    'method' => 'PUT', 'id' => 'updateHeadForm']) !!}
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
                {!! Form::text('name', $head->name, ['class'=>'form-control']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', $head->description, ['class'=>'form-control']) !!}
            </div>

            @if($head->type == 'expense')
                <div class="my-2">
                    {!! Form::checkbox('is_office_expense', 1, $head->is_office_expense) !!}
                    {!! Form::label('Is Office Expense') !!}
                </div>
            @endif

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

