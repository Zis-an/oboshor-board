<div class="modal-dialog">
    {!! Form::open(['url' => route('branches.update', $branch->id),
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
                    {!! Form::text('name', $branch->name, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('bank_id', 'Bank*', ['class' => 'form-label']) !!}
                    {!! Form::select('bank_id', $banks, $branch->bank_id, ['class' => 'form-control', 'placeholder' => 'Select Bank']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('phone', 'Phone*') !!}
                    {!! Form::text('phone', $branch->phone, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('email', 'Email*') !!}
                    {!! Form::text('email', $branch->email, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('address', 'Address*') !!}
                    {!! Form::text('address', $branch->address, ['class'=>'form-control']) !!}
                </div>


            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

