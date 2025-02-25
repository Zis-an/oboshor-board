@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush

@section('main')

    <div class="card">
        <div class="card-body row">

            <div class="col-sm-4">
                {!! Form::label('date', 'Date Range', ['class' => 'control-label']) !!}
                {!! Form::text('date', '', ['class' => 'form-control']) !!}
            </div>

            <div class="col-sm-4">
                {!! Form::label("head_id", 'Income Head *') !!}
                {!! Form::select("head_id", $incomeHeads, '', ['class' => 'form-control','placeholder' => 'Select Income Head', 'required' => 'required']) !!}
            </div>

            <div class="col-sm-4">
                {!! Form::label("head_item_id", 'Income Sub Head') !!}
                {!! Form::select("head_item_id", [], '', ['class' => 'form-control','placeholder' => 'Select Income Head']) !!}
            </div>
        </div>

    </div>

    <div id="table_container"></div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {

            $("#date").daterangepicker({
                start: moment().startOf('month'),
                end: moment(),
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            })

            /*let table = $("#income_table").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '/income-report',
                    data: {
                        date: $('#date').val(),
                        head_id: $('#head_id').val(),
                        head_item_id: $('#head_item_id').val()
                    }
                },
                columnDefs: [
                    {
                        targets: '_all',
                        orderable: false,
                        searchable: false,
                        defaultContent: '-',
                    },
                ],
                columns: [
                    {
                        "data": 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'date'},
                    {data: "item_name"},
                    {data: 'description'},
                    {
                        data: 'amount',
                        class: 'text-right'
                    },

                ],
            })*/

            $('#date, #head_id, #head_item_id').on('change', function () {
                //table.ajax.reload();
                getData();
            })

            $(document).on('change', '#head_id', function () {

                let head = this.value;

                $.ajax({
                    url: `/get-head-items?head_id=${head}`,
                    success: function (data) {

                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let options = '<option value="">Select Item Head</option>';
                        data.map(item => {
                            options += `<option value='${item.id}'>${item.name}</option>`
                        })

                        $('#head_item_id').html(options);

                    },
                    error: function () {

                    }
                })

            })

            function getData() {

                let date = $('#date').val();
                let head_id = $('#head_id').val();
                let head_item_id = $('#head_item_id').val();

                console.log({date, head_id, head_item_id});

                $.ajax({
                    url: window.location.pathname,
                    data: {
                        date,
                        head_id,
                        head_item_id
                    },
                    success: function (html) {
                        $("#table_container").html(html)
                        $("#income_table").DataTable({
                            columnDefs: [{
                                targets: '_all',
                                orderable: false,
                                searchable: false,
                            }]
                        })
                    }
                })
            }

            getData();

            $(document).on('click', '#export_btn_pdf', function () {

                let date = $('#date').val();

                let head_id = $('#head_id').val();
                let head_item_id = $('#head_item_id').val();

                let url = `/income-report-export?type=pdf&date=${date}&head_id=${head_id}&head_item_id=${head_item_id}`

                window.open(url, '_blank');

            })

            $(document).on('click', '#export_btn_excel', function () {

                let date = $('#date').val();

                let head_id = $('#head_id').val();
                let head_item_id = $('#head_item_id').val();

                let url = `/income-report-export?type=excel&date=${date}&head_id=${head_id}&head_item_id=${head_item_id}`

                window.open(url, '_blank');

            })

        })
    </script>
@endpush
