<a href="{{route('cheques.show',$row->id)}}" class="btn btn-info">View</a>
@if ($row->status == 'deposited')
    <button class='btn btn-primary complete-transaction-btn' data-href={{'/cheques/'.$row->id. '/complete-transaction'}}>Complete
        Transaction
    </button>
@elseif ($row->status == 'issued' || $row->status == 'received')
    <button class='btn btn-primary deposit-btn' data-href={{'/cheques/'.$row->id. '/deposit'}}>Deposit</button>
@endif
