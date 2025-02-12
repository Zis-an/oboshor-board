<td>
    <a href="{{route('leave-plans.edit', $row->id)}}"
       class="btn btn-primary"
    >Edit</a>
</td>

<td>
    <button class="btn btn-danger btn-sm delete-item-btn"
    data-href="{{route('leave-plans.destroy', $row->id)}}"
    >Delete</button>
</td>
