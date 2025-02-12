@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush


@section('main')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-end">
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

    <div id="bank_wise_report_container"></div>

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

            $('#filter_btn').on('click', function () {

                let date = $('#date').val();

                $.ajax({
                    url: window.location.path,
                    data: {
                        date: date,
                    },
                    success: function (html) {
                        $('#bank_wise_report_container').html(html);
                        $('#account_book_table').DataTable({})
                    }
                })


            })

            $(document).on('click', '#export_btn_pdf', function () {

                let date = $('#date').val();

                let url = `/bank-wise-report-export?type=pdf&date=${date}`

                window.open(url, '_blank');

            })

            $(document).on('click', '#export_btn_excel', function () {

                let date = $('#date').val();

                let url = `/bank-wise-report-export?type=excel&date=${date}`

                window.open(url, '_blank');

            })

        })

    </script>

@endpush
