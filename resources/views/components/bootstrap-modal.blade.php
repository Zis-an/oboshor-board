<div class="modal-dialog modal-{{$size ?? 'md'}}">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fs-5">{{$title}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            {{$slot}}
        </div>
        @if(!isset($hideFooter))
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-outline-primary">Close</button>
                @if(isset($submitButton))
                    <button type="submit" class="btn btn-primary">{{$submitButton}}</button>
                @endif
            </div>
        @endif
    </div>
</div>

