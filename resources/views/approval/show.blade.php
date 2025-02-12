<div class="modal-dialog">
    {!! Form::open(['url' => route('approvals.store'), 'id' => 'approval_form']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5" id="exampleModalLabel">View</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" value="{{$approval->id}}"/>
            <input type="hidden" value="approve" name="status" id="approval_status"/>
            <div class="my-2">
                {!! Form::label('comment', 'Comment*') !!}
                {!! Form::text('comment', '', ['class'=>'form-control', 'placeholder' => 'comment']) !!}
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="reject_btn">Reject</button>
            <button type="button" class="btn btn-primary" id="approve_btn">Approve</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
