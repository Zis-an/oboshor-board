<div class="row">    
    <div class="col-md-4 col-6">
        <div class="form-group">
            {!! Form::label('date_range', 'Date Range', ['class' => 'control-label']) !!}
            {!! Form::text('date_range', $request->date_range ?? $initialDateRange, ['class' => 'form-control', 'id' => 'date_range']) !!}
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="form-group">
            {!! Form::label('index', 'Index No', ['class' => 'control-label']) !!}
            {!! Form::text('index', $request->index ?? '', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="form-group">
            {!! Form::label('account', 'Account No', ['class' => 'control-label']) !!}
            {!! Form::text('account', $request->account ?? '', ['class' => 'form-control']) !!}
        </div> 
    </div>


    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="form-group">
                {!! Form::label('account_id', 'Bank Account', ['class' => 'control-label'])  !!}
                {!! Form::select('account_id', $accounts, $request->account_id ?? '' , ['class' => 'form-control select2', 'placeholder' => 'Select Account']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">

            <div class="form-group">
                {!! Form::label('lots_id', 'Lot Name', ['class' => 'control-label'])  !!}
                {!! Form::select('lots_id', $lots, $request->lots_id ?? '' , ['class' => 'form-control select2', 'placeholder' => 'Select Lot']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            {!! Form::label('lots_status', 'Status', ['class' => 'control-label']) !!}
            {!! Form::select('lots_status', $status, $request->lots_status ?? 'all' , ['class' => 'form-control select2', 'placeholder' => 'Select Status']) !!}
        </div>
    </div>

    <div class="col-12">
        <button class="btn btn-primary">Search</button>
    </div>
</div>