@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush
@section('main')

{{--    <div class="card">--}}
{{--        <div class="card-body">--}}
{{--            <div class="row align-items-end">--}}
{{--                <div class="col-sm-3">--}}
{{--                    {{Form::label('financial_year_id', 'Account')}}--}}
{{--                    {!! Form::select('financial_year_id', $financialYears, '', ['class' => 'form-control select2-search', 'placeholder' => 'Financial Year']) !!}--}}
{{--                </div>--}}
{{--                <div class="col-sm-3">--}}
{{--                    <button class="btn btn-primary" id="filter_btn" disabled>Filter</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

    <!-- Newly Added -->
    <div class="card">
        <div class="card-body">
            <div class="row align-items-end">
                <!-- Financial Year Dropdown -->
                <div class="col-sm-3">
                    <div class="form-group">
                        {{ Form::label('financial_year_id', 'Account') }}
                        {!! Form::select('financial_year_id', $financialYears, '', [
                            'class' => 'form-control select2-search',
                            'placeholder' => 'Financial Year'
                        ]) !!}
                    </div>
                </div>

                <!-- Multi Select Dropdown -->
                <div class="col-sm-6">
                    <div class="form-group">
                        {{ Form::label('current_job_role', 'Select Heads') }}
                        {!! Form::select('current_job_role[]', $heads->pluck('name', 'id')->toArray(), null, [
                            'id' => 'current-job-role',
                            'class' => 'form-control sd-CustomSelect',
                            'multiple' => 'multiple',
                        ]) !!}
                    </div>
                </div>

                <!-- Filter Button -->
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>&nbsp;</label> <!-- Empty label for spacing -->
                        <button class="btn btn-primary py-2" id="filter_btn" disabled>Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Newly Added -->

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

            // $('#financial_year_id').on('change', function () {
            //     if (this.value == '') {
            //         $('#filter_btn').attr('disabled', 'disabled')
            //     } else {
            //         $('#filter_btn').removeAttr('disabled');
            //     }
            // })

            // Newly Added
            function toggleFilterButton() {
                const financialYearSelected = $('#financial_year_id').val() !== ''; // Check if a financial year is selected
                const multiSelectSelected = $('#current-job-role').val() && $('#current-job-role').val().length > 0; // Check if at least one option is selected

                if (financialYearSelected && multiSelectSelected) {
                    $('#filter_btn').removeAttr('disabled'); // Enable button
                } else {
                    $('#filter_btn').attr('disabled', 'disabled'); // Disable button
                }
            }

            // Event listeners for both dropdowns
            $('#financial_year_id').on('change', toggleFilterButton);
            $('#current-job-role').on('change', toggleFilterButton);
            // Newly Added

            // $('#filter_btn').on('click', function () {
            //
            //     $.ajax({
            //         url: window.location.path,
            //         data: {
            //             financial_year_id: $('#financial_year_id').val(),
            //         },
            //         success: function (html) {
            //             $('#cashbook_report_container').html(html);
            //         }
            //     })
            // })

            // Newly Added
            $('#filter_btn').on('click', function () {
                const selectedHeads = $('#current-job-role').val(); // Get selected head IDs from the multi-select dropdown

                $.ajax({
                    url: window.location.pathname,
                    data: {
                        financial_year_id: $('#financial_year_id').val(),
                        heads: selectedHeads // Pass selected heads to the server
                    },
                    success: function (html) {
                        $('#cashbook_report_container').html(html);
                    }
                });
            });
            // Newly Added

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
