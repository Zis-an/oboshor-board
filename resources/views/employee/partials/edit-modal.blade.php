<div class="modal-dialog">
    {!! Form::open(['url' => route('employees.update', $employee->id),
    'method' => 'PUT', 'id' => 'updateEmployeeForm']) !!}
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
                    {!! Form::text('name', $employee->name, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('designation_id', 'Select Designation*', ['class' => 'form-label']) !!}
                    {!! Form::select('designation_id', $designations, $employee->designation_id, ['class' => 'form-control', 'placeholder' => 'Select Designation']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('salary_type', 'Salary Type*', ['class' => 'form-label']) !!}
                    {!! Form::select('salary_type', [ 'monthly' => 'Monthly', 'daily' => 'Daily' ], $employee->salary_type, ['class' => 'form-control', 'placeholder' => 'Select Salary Type']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('salary', 'Salary*') !!}
                    {!! Form::text('salary', $employee->salary, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('phone', 'Phone*') !!}
                    {!! Form::text('phone', $employee->phone, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('email', 'Email*') !!}
                    {!! Form::text('email', $employee->email, ['class'=>'form-control']) !!}
                </div>

                <div class="my-2">
                    {!! Form::label('address', 'Address*') !!}
                    {!! Form::text('address', $employee->address, ['class'=>'form-control']) !!}
                </div>

            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

