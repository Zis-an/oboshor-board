@can('bank.edit')
    <button class='btn btn-primary btn-sm edit-bank-btn' data-href="{{route('banks.edit', $row->id)}}">Edit</button>
@endcan
@can('bank.delete')
    <button class='btn btn-danger btn-sm delete-bank-btn' data-href="{{route('banks.destroy', $row->id)}}">Delete
    </button>
@endcan
