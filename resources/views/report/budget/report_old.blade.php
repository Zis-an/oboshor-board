@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush


@section('main')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-sm-3">
                    {{Form::label('financial_year_id', 'Account')}}
                    {!! Form::select('financial_year_id', $financialYears, '', ['class' => 'form-control select2-search', 'placeholder' => 'Financial Year']) !!}
                </div>

                <div class="col-sm-3">
                    <button class="btn btn-primary" id="filter_btn" disabled>
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
                }
            });


            $('#financial_year_id').on('change', function () {
                if (this.value == '') {
                    $('#filter_btn').attr('disabled', 'disabled')
                } else {
                    $('#filter_btn').removeAttr('disabled');
                }
            })

            $('#filter_btn').on('click', function () {

                $.ajax({
                    url: window.location.path,
                    data: {
                        financial_year_id: $('#financial_year_id').val(),
                    },
                    success: function (html) {
                        $('#cashbook_report_container').html(html);
                        //$('#account_book_table').DataTable({})
                    }
                })


            })

            $(document).on('click', '#export_btn_pdf', function () {

                console.log('export');

                let financialYear = $('#financial_year_id').val();

                let url = `/budget-report-export?type=pdf&fy=${financialYear}`;

                window.open(url, '_blank');

            })

            $(document).on('click', '#export_btn_excel', function () {

                let financialYear = $('#financial_year_id').val();
                let head_id = $('#head_id').val();
                let sub_head = $('#sub_head_id').val();

                let url = `/budget-report-export?type=excel&fy=${financialYear}&head=${head_id}&sub=${sub_head}`;

                window.open(url, '_blank');

            })

            $('#head_id').on('change', function () {
                let head_id = this.value;
                $.ajax({
                    url: `/get-head-items?head_id=${head_id}`,
                    success: function (data) {
                        let options = "<option value=''>--Select One--</option>";
                        data.forEach(function (item) {
                            options += `<option value='${item.id}'>${item.name}</option>`;
                        })
                        $('#sub_head_id').html(options);
                    }
                })
            })

        })

    </script>

@endpush
