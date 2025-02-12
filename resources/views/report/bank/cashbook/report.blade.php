@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush


@section('main')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-end">

                @if(request()->query('petty_cash'))
                    <div class="col-sm-4">
                        <input type="hidden"
                               name="account_id"
                               id="account_id"
                               value="{{$pettyCashAccount->id}}"
                        >
                        <input type="hidden"
                               id="is_petty_cash"
                               value="1"
                        >
                        <label>Account</label>
                        <input type="text" value="Petty Cash"
                               disabled
                               class="form-control"
                        />
                    </div>
                @else
                    <div class="col-sm-4">
                        {{Form::label('account_id', 'Account')}}
                        {!! Form::select('account_id', $accounts, '', ['class' => 'form-control select2-search', 'placeholder' => 'All Accounts']) !!}
                    </div>
                @endif

                <div class="col-sm-4">
                    {{Form::label('date', 'Date Range')}}
                    {!! Form::text('date', $financialYearRange, ['class' => 'form-control']) !!}
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-primary" id="filter_btn">
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="cashbook_report_container"></div>

@endsection


@push('scripts')
    <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#date').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                },
                ranges,
                alwaysShowCalendars: true,
            });

            let account = $('#account_id').val();

            if (!account) {
                $('#filter_btn').attr('disabled', 'disabled')
            }

            $('#account_id').on('change', function () {
                if (this.value == '') {
                    $('#filter_btn').attr('disabled', 'disabled')
                } else {
                    $('#filter_btn').removeAttr('disabled');
                }
            })

            $('#filter_btn').on('click', function () {

                let account = $('#account_id').val();
                let date = $('#date').val();

                $.ajax({
                    url: window.location.path,
                    data: {
                        date: date,
                        account_id: $('#account_id').val(),
                    },
                    success: function (html) {
                        $('#cashbook_report_container').html(html);
                        $('#account_book_table').DataTable({})
                    }
                })


            })

            $(document).on('click', '#export_btn_pdf', function () {

                let account = $('#account_id').val();
                let date = $('#date').val();
                let is_petty_cash = $('#is_petty_cash').val();

                let url = `/cashbook-report-export?type=pdf&account=${account}&date=${date}&cash=${is_petty_cash}`;

                window.open(url, '_blank');

            })

            $(document).on('click', '#export_btn_excel', function () {

                let account = $('#account_id').val();
                let date = $('#date').val();
                let is_petty_cash = $('#is_petty_cash').val();

                let url = `/cashbook-report-export?type=excel&account=${account}&date=${date}&cash=${is_petty_cash}`;

                window.open(url, '_blank');

            })

        })

    </script>

@endpush
