@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush
@section('main')
    <section class="content-header">
        <div class="container-fluid">
            @if($account->is_cash_account)
                <h4 class="text-center">Account Book of Petty Cash</h4>
            @else
                <div>
                    <h4 class="text-center">Account Book of {{$account->account_no}}</h4>
                    <h4 class="text-center">{{$account->bank->name}}</h4>
                    <h4 class="text-center">{{$account->branch->name}}</h4>
                </div>
            @endif

        </div>
    </section>


    <div class="card">
        <div class="card-body">
            {!! Form::label('date_range', 'Date Range', ['class' => 'control-label']) !!}
            {!! Form::text('date_range', $initialDateRange, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h4>Current Balance: {{number_format($account->balance, 2)}}</h4>
        </div>
        <div>
            <a href="{{route('deposits.create', ['acc' => $account->id, 'type' => 'profit'])}}" class="btn btn-success">Add Profit</a>
            <a href="{{route('deposits.create', ['acc' => $account->id])}}" class="btn btn-success">Add Deposit</a>
<!--            <a href="{{route('withdraws.create', ['acc' => $account->id])}}" class="btn btn-primary">Withdraw</a>-->
            <a href="{{route("cheques.create",['acc' => $account->id])}}" class="btn btn-danger">Issue Cheque</a>
            <a href="{{route("cheques.create",['acc' => $account->id, 'type' => 'rcv'])}}" class="btn btn-info">Receive Check</a>
            <a href="{{route('accounts.add-service-charge', ['acc' => $account->id])}}" class="btn btn-warning">Add Charge</a>
        </div>
    </div>

    <input id="account_id" type="hidden" name="account_id" value="{{$account->id}}"/>

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
                <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
            </div>
        </div>

        <div class="card-body">
            <table class="w-100 table" id="accountTypeTable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Narration</th>
                    <th>Type</th>
                    <th>Method</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>

    <div class="modal fade" id="addMoney" tabindex="-1" aria-hidden="true">
    </div>

@endsection

@push('scripts')

    <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}">

    </script>

    <script>
        $(document).ready(function () {

            $('#date_range').daterangepicker({
                //startDate: '2025/09/20',
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            });

            let id = $('#account_id').val();

            let table = $('#accountTypeTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: `/accounts/${id}/account-book`,
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                    }
                },
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    {data: 'date'},
                    {data: 'description'},
                    {data: 'type'},
                    {data: "method"},
                    {data: 'debit', className: 'text-right'},
                    {data: 'credit', className: "text-right"},
                    {data: 'balance', className: "text-right"},
                ],
            });
            //.buttons().container().appendTo('#accountTypeTable_wrapper .col-md-6:eq(0)');

            $('#date_range').on('change', function () {
                table.ajax.reload();
            })

            $(document).on('click', '#export_btn_pdf', function () {

                let date = $('#date_range').val();

                let url = window.location.pathname + `?export=true&type=pdf&date_range=${date}`;

                window.open(url, '_blank');

            })

            $(document).on('click', '#export_btn_excel', function () {

                let date = $('#date_range').val();

                let url = window.location.pathname + `?export=true&type=excel&date_range=${date}`;

                window.open(url, '_blank');

            })

        });

    </script>
@endpush
