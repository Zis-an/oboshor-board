<div class="modal-dialog">
    {!! Form::open(['url' => route('head-items.update', $headItem->id), 'id' => 'updateHeadItemForm', 'method' => 'PUT']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Update Item</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="my-2">
                {!! Form::label('name', 'Name*') !!}
                {!! Form::text('name', $headItem->name, ['class'=>'form-control']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('head_id', 'Expense Head *') !!}
                {!! Form::select('head_id', $heads, $headItem->head_id, ['class'=>'form-control', 'placeholder' => 'Select Account Head']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('description', 'Description*') !!}
                {!! Form::textarea('description', $headItem->description, ['class'=>'form-control']) !!}
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

