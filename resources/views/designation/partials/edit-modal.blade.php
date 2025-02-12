<div class="modal-dialog">
    {!! Form::open(['url' => route('designations.update', $designation->id),
    'method' => 'PUT', 'id' => 'updateBankForm']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5">Update</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="modal-body">

                <div class="my-2">
                    {!! Form::label('name', 'Name*') !!}
                    {!! Form::text('name', $designation->name, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('short', 'Short Name*') !!}
                    {!! Form::text('short', $designation->short, ['class'=>'form-control']) !!}
                </div>


            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

