@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush

@section('main')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('date', 'Date Range', ['class' => 'control-label']) !!}
                        {!! Form::text('date', '', ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">

        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title">Expense Report</h3>
            <div>
                <button id="export_btn" data-type="pdf" class="btn btn-primary btn-sm">PDF</button>
                <button id="export_btn" data-type="excel" class="btn btn-primary btn-sm">Excel</button>
            </div>
        </div>

        <div class="card-body">
            <table class="w-100 table" id="income_table">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Particular</th>
                    <th>Payment Method</th>
                    <th>Cheque No#</th>
                    {{--<th>For</th>--}}
                    <th>Amount</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            $("#date").daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            })

            let table = $("#income_table").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '/expense-report',
                    data: function (d) {
                        d.date = $('#date').val();
                    }
                },
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                    },
                    {
                        "targets": 0,
                        "data": null,
                        "title": "SL",
                        "render": function (data, type, row, meta) {
                            // 'meta.row' gives the row number starting from 0
                            console.log({data, type, row, meta})

                            let start = meta.settings._iDisplayStart;
                            return meta.row + start + 1;
                        }
                    }
                ],
                columns: [
                    {data: 'sl'},
                    {data: 'date'},
                    {data: 'title'},
                    {data: 'description'},
                    {data: 'method'},
                    {data: 'cheque_number'},
                    /*{data: 'for'},*/
                    {data: 'amount'},
                ],
            })

            $('#date').on('change', function () {
                table.ajax.reload();
            })

            $(document).on('click', '#export_btn', function () {

                let date = $('#date').val();
                let type= $(this).data('type');

                let url = `/expense-report?export=true&type=${type}&date=${date}`;

                window.open(url, '_blank');

            })

        })
    </script>
@endpush
