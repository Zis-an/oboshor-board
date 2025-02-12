@extends('layouts.app')

@section('main')

    <div>
        <div class="col-sm-4">
            {{Form::label('date', 'Date Range')}}
            {!! Form::text('date', '', ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="content-header">
        <h3>Lot Reports</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Sent</th>
                    <th>Hold</th>
                    <th>Returned</th>
                    <th>Stopped</th>
                    <th>Processing</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('#date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            });

            /*$.ajax({
                url: window.location.pathname,
                data: {
                    date_range: $('#date').val(),
                }
            })*/

            console.log('table', $('#table'));

            let table = $('#table').DataTable({
                ajax: {
                    url: window.location.pathname,
                    data: function (d) {
                        d.date_range = $('#date').val();
                    },
                },
                columnDefs: [
                    {
                        targets: '_all',
                        defaultContent: '-',
                    }
                ],
                columns: [
                    {data: 'date'},
                    {data: 'sent_count'},
                    {data: 'hold_count'},
                    {data: 'returned_count'},
                    {data: 'stopped_count'},
                    {data: 'processing_count'},
                ],

            })

            $('#date').on('change', function () {
                table.ajax.reload();
            })

        })
    </script>
@endpush
