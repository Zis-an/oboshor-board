@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush

@section('main')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-sm-4">
                    {{Form::label('account_id', 'Account')}}
                    {!! Form::select('account_id', $accounts, '', ['class' => 'form-control', 'placeholder' => 'All Accounts']) !!}
                </div>
                <div class="col-sm-4">
                    {{Form::label('date', 'Date Range')}}
                    {!! Form::text('date', '', ['class' => 'form-control']) !!}
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-primary" id="filter_btn">
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div id="lot_table_container"></div>

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

            let account = $('#account_id').val();
            let date = $('#date').val();

            $('#filter_btn').on('click', function () {

                $.ajax({
                    url: '/bank-report',
                    data: {
                        date: date,
                    },
                    success: function (data) {
                        let {lotTable} = data;
                        $('#lot_table_container').html(lotTable);
                        $('#lot_table').DataTable({});
                        $('#account_book_table').DataTable({})
                    }
                })


            })

        })
    </script>
@endpush
