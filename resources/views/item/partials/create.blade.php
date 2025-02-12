<div class="modal-dialog">
    {!! Form::open(['url' => route('items.store'), 'id' => 'createHeadItemForm']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Add Item</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="my-2">
                {!! Form::label('name', 'Name*') !!}
                {!! Form::text('name', '', ['class'=>'form-control', 'placeholder' => 'Item Name']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('head_id', 'Select Head *') !!}
                {!! Form::select('head_id', $heads, '', ['class'=>'form-control', 'placeholder' => 'Select Account Head', 'id' => 'selectHead']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('head_item_id', 'Select Head Item*') !!}
                {!! Form::select('head_item_id', [], '', ['class'=>'form-control', 'placeholder' => 'Select Account Head', 'id' => 'selectHeadItem']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', '', ['class'=>'form-control', 'rows' => 2, 'placeholder' => 'Description']) !!}
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

