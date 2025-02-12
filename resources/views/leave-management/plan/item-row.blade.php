<tr>
    <td>
        <input type="hidden" class="td-index" value="{{$index}}">
        <input type="hidden" class="td-employee-id" value="{{$employee->id}}"
        name="{{'plans['.$index.'][employee_id]'}}"
        >
        {{$employee->name}} {{($employee->designation_name)}}
    </td>
    <td>
        <input type="number" class="form-control"
               name="{{'plans['.$index.'][balance]'}}"
        />
    </td>
    <td></td>
</tr>
