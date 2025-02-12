@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Bank Accounts</h4>
                @can('bank.create')
                    <button data-href="{{route('accounts.create')}}" class="btn btn-primary create-account-btn">
                        <i class="fa fa-plus"></i>
                        Create
                    </button>
                @endcan
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            {!! Form::label('date', 'Date Range', ['class' => 'control-label']) !!}
            {!! Form::text('date', '', ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="w-100 table" id="accountTypeTable">
                <thead>
                <tr>
                    <th>Account No.</th>
                    <th>Account Type</th>
                    <th>Balance</th>
                    <th>Income</th>
                    <th>Expense</th>
                    <th>Bank</th>
                    <th>Branch</th>
                    <th>Status</th>
                </tr>
                </thead>
            </table>

        </div>
    </div>

@endsection

@push('scripts')
    <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {

            $("#date").daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            })

            let table = $('#accountTypeTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '/fdr-report',
                    data: function (d) {
                        d.date = $("#date").val();
                    },
                },

                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                        defaultContent: "-",
                        targets: "_all",
                    },
                ],
                columns: [
                    {data: 'account_no'},
                    {data: 'type'},
                    {data: 'balance', searchable: false},
                    {data: 'income', searchable: false},
                    {data: 'expense', searchable: false},
                    {data: 'bank.name', searchable: false},
                    {data: 'branch.name', searchable: false},
                    {data: 'status', searchable: false}
                ],
            });

            $('#date').on('change', function () {
                table.ajax.reload();
            })

        })

    </script>
@endpush
