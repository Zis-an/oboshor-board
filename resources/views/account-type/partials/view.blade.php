@component('components.bootstrap-modal', ['size' => 'md', 'title' => 'View Account Type'])

    <table class="table table-borderless">
        <tr>
            <th>
                Account Type
            </th>
            <td>
                {{$accountType->name}}
            </td>
        </tr>
        <tr>
            <th>
                Allow Withdraw
            </th>
            <td>{{$accountType->allow_withdraw ? 'True' : 'False'}}</td>
        </tr>
        <tr>
            <th>
                Allow Deposit
            </th>
            <td>{{$accountType->allow_deposit ? 'True' : 'False'}}</td>
        </tr>
        <tr>
            <th>
                Has Interest
            </th>
            <td>{{$accountType->has_interest ? 'True' : 'False'}}</td>
        </tr>
        <tr>
            <th>
                Has Maturity Period
            </th>
            <td>{{$accountType->has_maturity_period ? 'True' : 'False'}}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <td>{{$accountType->createdBy->name}}</td>
        </tr>
    </table>

    <h5>Description</h5>
    <p>{{$accountType->description}}</p>

@endcomponent
