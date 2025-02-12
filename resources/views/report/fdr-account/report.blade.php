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
                    {!! Form::select('financial_year_id', $financialYears, '', ['class' => 'form-control select2-search', 'placeholder' => 'Financial Year', 'id' => 'financial_year_id']) !!}
                </div>

                <div class="col-sm-3">
                    {!! Form::label('date', 'Date Range', ['class' => 'control-label']) !!}
                    {!! Form::text('date', '', ['class' => 'form-control', 'id' => 'date']) !!}
                </div>

                <div class="col-sm-3">
                    <button class="btn btn-primary" id="filter_btn">Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div id="cashbook_report_container"></div>

@endsection


@push('scripts')
    <script type="text/javascript" src="{{ asset('adminLTE/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#financial_year_id').on('change', function () {
                const selectedValue = $(this).val();
                const selectedText = $(this).find('option:selected').text();

                if (selectedValue === '') {
                    $('#filter_btn').attr('disabled', 'disabled');
                } else {
                    $('#filter_btn').removeAttr('disabled');
                }
            });

            $('#filter_btn').on('click', function () {
                $.ajax({
                    url: window.location.pathname, // Corrected typo from "path" to "pathname"
                    data: {
                        fy: $("#financial_year_id").val(),
                        date_range: $("#date").val(), // Added the date range
                    },
                    success: function (html) {
                        $('#cashbook_report_container').html(html);
                    },
                    error: function (xhr) {
                        console.error("Error occurred:", xhr);
                        alert("Failed to fetch data. Please try again.");
                    }
                });
            });

            $(document).on('click', '#export_btn_pdf', function () {
                let fy = $("#financial_year_id").val();
                let url = window.location.pathname+`?export=true&type=pdf&fy=${fy}`;
                window.open(url, '_blank');
            })

            $(document).on('click', '#export_btn_excel', function () {
                let fy = $("#financial_year_id").val();
                let url = window.location.pathname+`?export=true&type=excel&fy=${fy}`;
                window.open(url, '_blank');
            })

        })
    </script>
    <script>
        $(document).ready(function () {
            // Initialize the date range picker with default values
            $("#date").daterangepicker({
                autoUpdateInput: false, // Do not auto-fill the input field
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            });

            // Listen for changes in the financial year dropdown
            $('#financial_year_id').on('change', function () {
                const selectedYear = $(this).find('option:selected').text(); // Get selected financial year text

                if (selectedYear) {
                    // Parse the financial year text (e.g., "2020-2021")
                    const [startYear, endYear] = selectedYear.split('-').map(year => parseInt(year.trim()));

                    if (startYear && endYear) {
                        // Define the new date range limits
                        const startDate = moment(`01-07-${startYear}`, 'DD-MM-YYYY');
                        const endDate = moment(`30-06-${endYear}`, 'DD-MM-YYYY');

                        // Destroy the existing daterangepicker and reinitialize it with new limits
                        $("#date").daterangepicker({
                            startDate: startDate,
                            endDate: endDate,
                            minDate: startDate,
                            maxDate: endDate,
                            locale: {
                                format: 'YYYY-MM-DD',
                                separator: '~',
                            }
                        });
                    }
                }
            });
        });
    </script>
@endpush
