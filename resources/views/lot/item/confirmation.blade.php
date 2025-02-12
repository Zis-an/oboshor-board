<x-bootstrap-modal title="{{$type}} this Item" :hideFooter="false">
    <form id="confirmation_form">
        <input type="hidden" name="selected" value="{{$item->id}}"/>
        <input type="hidden" name="type" value="{{$type}}"/>
        <div class="row">
            @if($type == 'returned')
                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label('with_credit', 'With Credit', ['control-label']) !!}
                        {!! Form::select('with_credit', [1 => 'With Credit', 0 => 'Without Credit'], '', ['class' => 'form-control', 'placeholder' => 'Select', 'required' => 'true']) !!}
                    </div>
                </div>
            @endif

            @if($type == 'sent' || $type == 'returned' || $type == 'resend')
                <div class="col-12">
                    {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                    {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                </div>
            @endif

            <div class="col-12">
                <div class="form-group">
                    {!! Form::label('comment', 'Comment', ['control-label']) !!}
                    {!! Form::textarea('comment', '', ['rows' => 4, 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</x-bootstrap-modal>
