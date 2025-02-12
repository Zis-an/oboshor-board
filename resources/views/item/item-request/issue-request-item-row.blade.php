<tr>
    <input type="hidden" name="index" value="{{$index}}"/>
    <input type="hidden" name="{{'items['. $index . '][item_id]'}}" value="{{$item->item_id}}" />
    <input type="hidden" name="{{'items['. $index . '][inventory_request_item_id]'}}" value="{{$item->id}}" />
    <td>
        {{$item->item->name ?? ''}}
    </td>
    <td>
        <h4>{{$item->quantity}}</h4>
    </td>
    <td>
        {!! Form::text('items['. $index . '][quantity]', '', ['class' => 'form-control']) !!}
    </td>
    <td>
        {{$item->priority}}
    </td>
</tr>
