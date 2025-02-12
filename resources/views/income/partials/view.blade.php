@component('components.bootstrap-modal', ['title' => 'View Income'])
    <div>
        <table class="table w-100">
            <tr>
                <th>
                    Date:
                </th>
                <td>
                    {{\Carbon\Carbon::parse($income->date)->format('d-m-Y')}}
                </td>
            </tr>
            <tr>
                <th>Amount:</th>
                <td>{{number_format($income->amount, 2)}}</td>
            </tr>
            <tr>
                <th>
                    Head:
                </th>
                <td>{{$income->head->name}}</td>
            </tr>

            <tr>
                <th>
                    Head Item:
                </th>
                <td>{{$income->headItem ? $income->headItem->name : ''}}</td>
            </tr>

            <tr>
                <th>Created By</th>
                <td>{{$income->createdBy->name}}</td>
            </tr>
            <tr>
                <th>Deposit Bank Account</th>
                <td>{{$income->account->name}}</td>
            </tr>

            <tr>
                <th>
                    Transaction Method
                </th>
                <td>
                    {{$income->method}}
                </td>
            </tr>

            @if($income->method == 'cheque')
                <tr>
                    <th>
                        Bank
                    </th>
                    <td>{{$income->bank}}</td>
                </tr>
                <tr>
                    <th>
                        Cheque Number
                    </th>
                    <td>
                        {{$income->cheque_number}}
                    </td>
                </tr>

            @endif

            @if($income->method === 'pay-order')
                <tr>
                    <th>
                        Bank
                    </th>
                    <td>{{$income->bank}}</td>
                </tr>
                <tr>
                    <th>
                        Pay Order Number
                    </th>
                    <td>
                        {{$income->pay_order_number}}
                    </td>
                </tr>

            @endif

        </table>
    </div>
@endcomponent
