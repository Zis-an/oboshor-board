<td>
    <button data-href="{{route('leave-requests.show', $row->id)}}"
            class="btn btn-primary bnt-sm view-btn"
    >View
    </button>
    @if($row->user_id == auth()->id() && in_array($row->status, ['pending', 'rejected']))
        <a href="{{ route('leave-requests.edit', $row->id) }}"
        class="btn btn-primary btn-sm"
        >Edit</a>
        <button data-href="{{ route('leave-requests.destroy', $row->id) }}"
        class="btn btn-danger btn-sm delete-item-btn"
        >Delete</button>
    @endif

    @if($row->status =='pending')
        @can('leave-request.approve')
            <button class="btn btn-success btn-approve"
                    data-href="{{route('leave-requests.update-status', $row->id)}}"
            >Approve
            </button>
        @endcan
        @can('leave-request.reject')
            <button class="btn btn-danger btn-reject"
                    data-href="{{route('leave-requests.update-status', $row->id)}}"
            >Reject
            </button>
        @endcan
    @endif
</td>
