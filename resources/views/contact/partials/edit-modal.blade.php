<div class="modal-dialog">
    {!! Form::open(['url' => route('contacts.update', $contact->id),
    'method' => 'PUT', 'id' => 'updateEmployeeForm']) !!}
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
                {!! Form::text('name', $contact->name, ['class'=>'form-control', 'placeholder' => 'Name']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('phone', 'Phone*') !!}
                {!! Form::text('phone', $contact->phone, ['class'=>'form-control', 'placeholder' => 'Phone']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('address', 'Address*') !!}
                {!! Form::text('address', $contact->address, ['class'=>'form-control', 'placeholder' => 'Address']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('email', 'Email') !!}
                {!! Form::text('email', $contact->email, ['class'=>'form-control', 'placeholder' => 'Email']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('website', 'Website') !!}
                {!! Form::text('website', $contact->website, ['class'=>'form-control', 'placeholder' => 'Website']) !!}
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

