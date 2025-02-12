@component('components.bootstrap-modal', ['title' => 'View Account', 'submitButton' => 'Submit'])
    <table class="table table-bordered">
        <tr>
            <th>Account Name:</th>
            <td>{{$account->name}}</td>
        </tr>
        <tr>
            <th>Account Number:</th>
            <td>{{$account->account_no}}</td>
        </tr>
        <tr>
            <th>Account Balance:</th>
            <td>BDT {{$account->balance}}</td>
        </tr>
        <tr>
            <th>Yearly Interest Rate:</th>
            <td>{{$account->interest_rate ?? ('Not Applicable')}}%</td>
        </tr>
        <tr>
            <th> Maturity Period (Months):</th>
            <td>{{$account->maturity_period ?? ('Not Applicable')}}</td>
        </tr>
        <tr>
            <th>Account Type:</th>
            <td class="text-capitalize">{{$account->type}}</td>
        </tr>
        <tr>
            <th>Bank Name:</th>
            <td>{{$account->bank->name}}</td>
        </tr>
        <tr>
            <th>Branch Name:</th>
            <td>{{$account->branch->name}}</td>
        </tr>
        <tr>
            <th>Branch Address</th>
            <td>{{$account->branch->address}}</td>
        </tr>
    </table>
@endcomponent
