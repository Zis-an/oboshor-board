<div class="modal-dialog">
    {!! Form::open(['url' => route('financial-years.update', $financialYear->id),
    'method' => 'PUT', 'id' => 'updateFinancialYearForm']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">Update Financial Year</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <div class="my-2">
                {!! Form::label('name', 'Name*') !!}
                {!! Form::text('name', $financialYear->name, ['class'=>'form-control']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('start_date', 'Start Date*') !!}
                {!! Form::date('start_date', $financialYear->start_date, ['class'=>'form-control', 'placeholder' => 'Start Date']) !!}
            </div>

            <div class="my-2">
                {!! Form::label('end_date', 'End Date*') !!}
                {!! Form::date('end_date', $financialYear->end_date, ['class'=>'form-control', 'placeholder' => 'End Date']) !!}
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

