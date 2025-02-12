@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush


@section('main')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-end">
                {{--<div class="col-sm-4">
                    {{Form::label('date', 'Date Range')}}
                    {!! Form::text('date', '', ['class' => 'form-control']) !!}
                </div>--}}
                <div class="col-sm-4">
                    {{Form::label('account_id', 'Account')}}
                    {!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2-search', 'id' => 'account_id', 'placeholder' => 'Select Account']) !!}
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-primary" id="filter_btn">
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="bank_wise_report_container"></div>

@endsection


@push('scripts')
    <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('#account_id').select2({})

            $('#date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            });

            getData();

            $('#filter_btn').on('click', function () {
                getData();
            })

            $(document).on('click', '#export_btn_pdf', function () {

                let date = $('#date').val();
                let account_id = $('#account_id').val();

                let url = `/lot-wise-report?export=true&type=pdf&account_id=${account_id}&date=${date}`

                window.open(url, '_blank');

            })

            $(document).on('click', '#export_btn_excel', function () {

                let date = $('#date').val();
                let account_id = $('#account_id').val();

                let url = `/lot-wise-report?export=true&type=excel&account_id=${account_id}&date=${date}`

                window.open(url, '_blank');

            })


        })

        function getData() {
            $.ajax({
                url: window.location.path,
                data: {
                    account_id: $('#account_id').val(),
                },
                success: function (html) {
                    $('#bank_wise_report_container').html(html);
                    $('#lot_table').DataTable({})
                }
            })
        }

    </script>

@endpush
