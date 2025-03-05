@if( strpos($row->comment, 'Transferred to') === false)
    <div class='btn-group'>
    <button class='btn btn-info btn-sm dropdown-toggle' type='button' data-toggle='dropdown'>Action</button>
    <div class='dropdown-menu'>
        <button class="dropdown-item action-btn" data-id="{{$row->id}}" data-type="view">
            <i class="fa fa-eye"></i>&nbsp;View
        </button>

        @if($row->status == 'processing' || strpos($row->comment, 'Transferred to') === false)
            <button class="dropdown-item action-btn" data-id="{{$row->id}}" data-type="sent">
                <i class="fa fa-"></i>
                Send</button>
        @endif
        @if($row->status == 'processing' || $row->status == 'returned' || strpos($row->comment, 'Transferred to') === false)
            <button class="dropdown-item action-btn" data-id="{{$row->id}}" data-type="hold">Hold</button>
            <button class="dropdown-item action-btn" data-id="{{$row->id}}" data-type="stop">Stop</button>
        @endif
        @if ($row->status == 'sent')
            <button class="dropdown-item action-btn" data-id="{{$row->id}}" data-type="returned">Return</button>
        @endif
        @if ($row->status == 'hold' || $row->status == 'returned' || $row->status == 'stop')
            <button class="dropdown-item action-btn" data-id="{{$row->id}}" data-type="sent">Send</button>
        @endif
    </div>
</div>
@endif
