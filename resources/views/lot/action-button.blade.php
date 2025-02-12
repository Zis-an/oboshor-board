<a class="btn btn-primary btn-sm" href="{{route('lots.show', $row->id)}}">View</a>
<a class='btn btn-info btn-sm' href='{{route("lots.edit", $row->id)}}'>Edit</a>
{{--@if($row->excel_file)
    <a class='btn btn-info btn-sm' href='{{asset($row->excel_file)}}'
    target="_blank">Excel</a>
@endif

@if($row->approval_file)
    <a href='{{asset($row->approval_file)}}'
       class="btn btn-primary btn-sm" target="_blank">Approved</a>
@endif--}}
