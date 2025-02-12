<tr>
    <input type="hidden" name="index" value="{{$index}}"/>
    <td>
        {!! Form::select('items['. $index . '][item_id]', $items, '', ['class' => 'form-control select2']) !!}
    </td>
    <td>
        {!! Form::text('items['. $index . '][quantity]', '', ['class' => 'form-control']) !!}
    </td>
    <td>
        <button type="button" class="btn btn-danger remove-item-btn">
            <i class="fa fa-trash"></i>
        </button>
    </td>
</tr>
