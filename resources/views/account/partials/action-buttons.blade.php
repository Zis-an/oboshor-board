<div class='btn-group'>
    <button class='btn btn-info btn-sm dropdown-toggle' type='button' data-toggle='dropdown'>
        Action
    </button>
    <div class='dropdown-menu'>
        @can('bank.view')
            <button class='dropdown-item view-account-btn' data-href='{{route('accounts.show', $row->id)}}'>
                <i class='fa fa-eye mr-2'></i>View
            </button>
        @endcan
        @can('bank.view')
            <a class='dropdown-item' href="{{'/accounts/'. $row->id .'/interests'}}">
                <i class='fa fa-eye mr-2'></i>View Interests</a>
        @endcan
        @can('accounting.account-book')
            <a class='dropdown-item' href="{{'/accounts/'.$row->id.'/account-book'}}">
                <i class='fa fa-bars mr-2'></i>Account Book</a>
        @endcan
        @can('accounting.service-charge')
            <button class='dropdown-item add-charge-btn' data-href="{{'/accounts/'. $row->id. '/charge'}}">
                <i class='fa fa-eye mr-2'></i>Add Service Charge
            </button>
        @endcan
        {{--
        @can('accounting.deposit')
            <button class='dropdown-item deposit-money-btn' data-href="{{'/account-deposits/create?id='.$row->id}}">
                <i class='fa fa-money-bill mr-2'></i>Deposit Money
            </button>
        @endcan
            --}}
        @can('accounting.deposit')
            <button class='dropdown-item edit-opening-balance-btn'
                    data-href="{{'/accounts/'. $row->id. '/edit-opening-balance'}}">
                <i class='fa fa-money-bill mr-2'></i>Edit Opening Balance
            </button>
        @endcan
        {{--
            @can('accounting.withdraw')
                @if($row->type != 'FDR')
                    <button class='dropdown-item withdraw-money-btn'
                            data-href="{{'/account-withdraws/create?id='. $row->id}}">
                        <i class='fa fa-money-check mr-2'></i>Withdraw Money
                    </button>
                @endif
            @endcan
            --}}

        @can('accounting.transfer')
            <button class="dropdown-item transfer-money-btn" data-href="{{'/account-transfers/create?id='. $row->id}}">
                <i class='fa fa-money-check mr-2'></i>Transfer Money
            </button>
        @endcan

        @can('bank.edit')
            <button class="dropdown-item btn-sm edit-account-btn" data-href="{{route('accounts.edit', $row->id)}}"><i
                    class='fa fa-edit mr-2'></i>Edit
            </button>
        @endcan
    </div>
</div>
