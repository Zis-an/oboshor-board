@extends('layouts.app')

@push('css')
    <link href="{{asset('/adminLTE/plugins/datatables-select/css/select.bootstrap4.min.css')}}">
@endpush

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Lot Items</h4>
                <a href="{{route('add-lot-item.create', $lot->id)}}" id="add_lot_item" class="btn btn-primary">Add New
                    Item
                </a>
            </div>
        </div>
    </section>

    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-sm-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Lot Name</th>
                            <td>{{$lot->name}}</td>
                        </tr>
                        <tr>
                            <th>
                                Total Amount
                            </th>
                            <td>
                                {{number_format($lot->items_sum_amount, 2)}}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-6">
                    <h4>Files</h4>

                    @include('partials.file-list', ['files' =>  explode('|', $lot->approval_file)])
                </div>
            </div>
        </div>
    </div>

    <section class="card">

        <div class="card-header">
            <div>
                <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
                <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-responsive" id="lotItemsTable">
                <thead>
                <tr>
                    <th>
                        <input type="checkbox" name="check_all" id="check_all"/>
                    </th>
                    <th>
                        Actions
                    </th>
                    <th>
                        Index
                    </th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>
                        Receiver Name
                    </th>
                    <th>
                        Bank Acc.
                    </th>
                    <th>District</th>
                    <th>Branch</th>
                    <th>
                        Amount
                    </th>
                    <th>
                        Bank Name
                    </th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>

            <hr/>

            <div>

                <div id="selected_item"></div>

                <div>
                    <button class="btn btn-primary" id="send_beftn_btn">Send EFT</button>
                    <!--                <button class="btn btn-warning" id="hold_items_btn">Hold</button>
                                        <button class="btn btn-danger">Return</button>
                                        <button class="btn btn-info" id="resend_btn">Resend</button>-->
                </div>
            </div>

        </div>
    </section>

    <div class="modal fade" id="viewLotItemModal" tabindex="-1" aria-hidden="true">
    </div>

    <div class="modal fade" id="holdLotItemModal" tabindex="-1" aria-hidden="true">

    </div>

    <div class="modal fade" id="sendModal" tabindex="-1" aria-hidden="true">
        <x-bootstrap-modal title="Send Payment" :hideFooter="true">
            <form id="send_form">
                <div class="row">
                    <div class="col-12">
                        {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                        {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                    </div>

                    <hr/>

                    <div class="col-12 mt-4">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </x-bootstrap-modal>
    </div>

    <x-modal-fade id="add_lot_item_modal"></x-modal-fade>

@endsection

@push('scripts')

    <script src="{{asset('/adminLTE/plugins/datatables-select/js/dataTables.select.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            let table = $('#lotItemsTable').DataTable({
                    ajax: window.location.pathname,
                    'columns': [
                        {
                            data: "checkbox",
                            orderable: false
                        },
                        {
                            data: 'actions',
                            orderable: false
                        },
                        {
                            data: 'index',
                            orderable: false
                        },
                        {
                            data: 'status',
                            orderable: false
                        },
                        {data: 'date',},
                        {
                            data: 'receiver_name',
                        },
                        {data: 'account_no'},
                        {data: "city"},
                        {data: 'branch_name'},
                        {
                            data: 'amount',
                        }, {
                            data: 'bank_name',
                        }
                    ]
                }
            );

            $(document).on('click', '.edit-expense-head-btn', function () {
                $('#updateExpenseHeadModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.view-lot-item-btn', function () {
                $('#viewLotItemModal').load($(this).data('href'), function (result) {
                    $(this).modal('show');
                })
            });

            $(document).on('click', '.row-select', function () {
                showAndCountChecked();
            })

            function showAndCountChecked() {
                let checkedCount = $('#lotItemsTable').find('input.row-select:checked').length;
                console.log(checkedCount);
                $('#selected_item').text(`${checkedCount} Items Selected`)
            }

            $(document).on('click', '#hold_items_btn', function () {
                let values = getSelectedItems();
                holdItems(values)
            })

            $(document).on('click', '#send_beftn_btn', function () {
                let values = getSelectedItems();
                $("#sendModal").modal('show');
                //sendItems(values)
            })

            $(document).on('click', '#resend_btn', function () {
                let values = getSelectedItems();
                resendItems(values)
            })

            function getSelectedItems() {
                let values = [];
                $('#lotItemsTable input.row-select:checked').each(function (index, el) {
                    values.push($(this).val());
                })
                return values;
            }

            //hold item

            $(document).on('click', '.action-btn', function () {
                let id = $(this).data('id');
                let type = $(this).data('type');
                let url = window.location.pathname + '/confirmation?id=' + id + '&type=' + type;
                $('#holdLotItemModal').load(url, function () {
                    $(this).modal('show');

                    //then add date picker

                    $('.date-time-picker').datetimepicker({
                        format: 'yyyy-MM-DD HH:mm:ss',
                        icons: {
                            time: "fa fa-clock",
                            date: "fa fa-calendar",
                            up: "fa fa-caret-up",
                            down: "fa fa-caret-down",
                            previous: "fa fa-caret-left",
                            next: "fa fa-caret-right",
                            today: "fa fa-today",
                            clear: "fa fa-clear",
                            close: "fa fa-close"
                        }
                    })

                })
            })

            $(document).on('submit', '#confirmation_form', function (e) {
                e.preventDefault()


                let formDataArray = $(this).serializeArray();
                let formDataObject = {};

                // Convert the form data array to an object
                $.each(formDataArray, function (index, field) {
                    formDataObject[field.name] = field.value;
                });

                let id = formDataObject.id;

                let type = $(this).find("input[name='type']").val();
                let withCredit = $(this).find("input[name='with_credit']").val();

                console.log({withCredit})

                switch (type) {
                    case 'sent':
                        sendItems(formDataObject);
                        break;
                    case 'hold':
                        holdItems(formDataObject);
                        break;
                    case 'returned':
                        returnItem(formDataObject);
                        break;
                    case 'resend':
                        resendItems(formDataObject);
                        break;
                    default:
                        sendItems();
                }

            })


            $(document).on('submit', 'form#send_form', function (event) {
                event.preventDefault();
                let date = $(this).find('input[name="date"]').val();
                let values = getSelectedItems();
                console.log('lll');
                sendItems(values, date);
                $('#sendModal').modal('hide');
                $(this).find('input[name="date"]').val('');
            });

            //actions
            //multiple
            function sendItems(values, date) {
                $.ajax({
                    method: 'POST',
                    url: window.location.pathname + '/send-beftn',
                    data: {
                        items: values,
                        date,
                    },
                    success: function (data) {
                        if (data.status === 'success') {
                            toastr.success(data.message)
                            table.ajax.reload();
                        } else {
                            toastr.error(data.message)
                        }
                    }
                })
            }


            $('#check_all').on('change', function () {
                $('#lotItemsTable input[type="checkbox"]:not(:disabled)').prop('checked', $(this).is(':checked'));
                showAndCountChecked();
            });


            function resendItems(data) {
                $.ajax({
                    method: 'POST',
                    url: window.location.pathname + '/resend',
                    data: data,
                    success: function (data) {
                        if (data.status === 'success') {
                            $('#holdLotItemModal').modal('hide');
                            toastr.success(data.message)
                            table.ajax.reload();
                        } else {
                            toastr.error(data.message)
                        }
                    }
                })
            }

            function holdItems(data) {
                $.ajax({
                    method: 'POST',
                    url: '/hold-lot-item',
                    data: data,
                    success: function (data) {
                        if (data.status === 'success') {
                            toastr.success(data.message)
                            table.ajax.reload();
                        } else {
                            toastr.error(data.message)
                        }
                    }
                })
            }

            function returnItem(data) {

                $.ajax({
                    method: 'POST',
                    url: window.location.pathname + '/return-item',
                    data: data,
                    success: function (data) {
                        if (data.status === 'success') {
                            toastr.success(data.message)
                            table.ajax.reload();
                        } else {
                            toastr.error(data.message)
                        }
                    }
                })
            }

           /* $('#add_lot_item').on('click', function () {
                $('#add_lot_item_modal').load($(this).data('href'), function () {
                    $(this).modal('show');
                })
            })*/

            $(document).on('click', '#export_btn_pdf', function () {

                let account_id = $('#account_id').val();

                let url = window.location.pathname + '?export=true' + `&type=pdf`;

                window.open(url, '_blank');

            })

            $(document).on('click', '#export_btn_excel', function () {

                let account_id = $('#account_id').val();

                let url = window.location.pathname + '?export=true' + `&type=excel`;

                window.open(url, '_blank');

            })

        })
    </script>
@endpush
