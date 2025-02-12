<x-bootstrap-modal title="Leave Request">
    <div>
        @php
            $plan = $leaveRequest->plan;
        @endphp
        <div class="alert alert-primary">
            {{$plan->balance - $plan->taken}} days left
        </div>

        <table class="table table-bordered">
            <tr>
                <th>Employee Name</th>
                <td>{{$leaveRequest->user->name}}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{$leaveRequest->start_date}}</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>{{$leaveRequest->end_date}}</td>
            </tr>
            <tr>
                <th>Reason</th>
                <td>{{$leaveRequest->reason}}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{$leaveRequest->status}}</td>
            </tr>
            <tr>
                <th>File</th>
                <td>
                    @if(!empty($leaveRequest->file))
                        <a href="{{asset($leaveRequest->file)}}"
                           class="btn btn-primary"
                           target="_blank"
                        >Open File</a>
                    @else
                        No File
                    @endif
                </td>
            </tr>
        </table>
    </div>
</x-bootstrap-modal>
