@component('components.bootstrap-modal',  ['title' => 'View Lot Item', 'size' => 'xl',])
    <div>
        <table class="table table-bordered">
            <thead class="thead-light">
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Index</th>
                <th scope="col">Name</th>
                <th scope="col">Amount</th>
                <th scope="col">Lot Name</th>
                <th scope="col">From Account</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ \Carbon\Carbon::parse($lotItem->date)->format('d-m-Y') }}</td>
                <td>{{$lotItem->index}}</td>
                <td>{{$lotItem->receiver_name}}</td>
                <td class="text-right">{{number_format($lotItem->amount, 2)}}</td>
                <td>{{$lot->short_name }}</td>
                <td>{{ $bankAccount->account_no }}</td>
                <td>
                    @if ($lotItem->status == 'sent')
                        <span class="badge badge-success">Sent</span>
                    @elseif ($lotItem->status == 'hold')
                        <span class="badge badge-warning">Hold</span>
                    @elseif ($lotItem->status == 'returned')
                        <span class="badge badge-danger">Returned</span>
                    @elseif ($lotItem->status == 'stop')
                        <span class="badge badge-danger">Stopped</span>
                    @else
                        <span class="badge badge-primary">Processing</span>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
        <h3>Files</h3>
        <table class="table table-bordered">
            <tr>
                <th>
                    Stop File
                </th>
                <td>
                    @if($lotItem->stop_file)
                        <a href="{{ asset($lotItem->stop_file)  }}" target="_blank">Open File</a>
                    @else
                        <span class="badge badge-danger">No File</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Hold File</th>
                <td>
                    @if($lotItem->hold_file)
                        <a href="{{ asset($lotItem->hold_file)  }}" target="_blank">Open File</a>
                    @else
                        <span class="badge badge-danger">No File</span>
                @endif
            </tr>
            <tr>
                <th>Return File</th>
                <td>
                    @if($lotItem->return_file)
                        <a href="{{ asset($lotItem->return_file)  }}" target="_blank">Open File</a>
                    @else
                        <span class="badge badge-danger">No File</span>
                @endif
            </tr>
        </table>
        <h3>BEFTN History</h3>

        @if ($transactionItems)
            <table class="table table-bordered table-responsive">
                <thead class="thead-light">
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Naraation</th>
                    <th scope="col">dr. Amount</th>
                    <th scope="col">cr. mount</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($transactionItems as $key => $val)
                    <tr>
                        <td>{{\Carbon\Carbon::parse($val->date)->format('d-m-Y')}}</td>
                        @php
                            $status = 'Sent';
                            if ($val->type) {
                            $status = $val->type;
                            }
                        @endphp

                        <td>{{$status}}</td>
                        @php
                            $dr_amn = 0;
                            $cr_amn = 0;
                            if ($val->account_type == 'credit') {
                            $cr_amn = $val->amount;
                            }
                            if ($val->account_type == 'debit') {
                            $dr_amn = $val->amount;
                            }
                        @endphp
                        <td>{{ $val->description}}</td>

                        <td class="text-right">{{number_format($dr_amn, 2) }}</td>
                        <td class="text-right">{{ number_format($cr_amn, 2)}}</td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-transaction-btn"
                                    data-href="{{route('lot-item-transactions.edit', [$lotItem->id, $val->id])}}">Edit
                            </button>
                        </td>
                    </tr>

                    @if ($lotItem->status == 'stop')
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($lotItem->updated_at)->format('d-m-Y') }}</td>
                            @php
                                $status = 'Stopped';
                            @endphp
                            <td>{{$status }}</td>
                            @php
                                $dr_amn = 0;
                                $cr_amn = 0;
                                if ($val->account_type == 'credit') {
                                $cr_amn = $val->amount;
                                }
                                if ($val->account_type == 'debit') {
                                $dr_amn = $val->amount;
                                }
                            @endphp

                            <td>{{ $lotItem->comment}}</td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td>
                                <button class="btn btn-primary btn-sm edit-transaction-btn"
                                        data-href="{{route('lot-item-transactions.edit', [$lotItem->id, $val->id])}}">Edit
                                </button>
                            </td>
                        </tr>
                    @endif

                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endcomponent
