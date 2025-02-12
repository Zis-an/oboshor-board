@extends('layouts.app')
@push('css')

@endpush
@section('main')

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Teacher/Employee Search Result</h4>
            </div>
        </div>
    </section>
    <div class="card">
        <div class="card-body">

            {!! Form::open(['url' => route('lots-post-search')]) !!}

            @include('lot.search.search')
            {!! Form::close() !!}
        </div>
    </div>

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Search Results</h4>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <table class="table border" id="lotItemsTable">
                <thead>
                <tr>
                    <th>
                        SL.
                    </th>
                    <th>
                        Action
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
                        Account No.
                    </th>
                    <th>
                        Amount
                    </th>
                    <th>
                        Lot Name
                    </th>
                    <th>
                        Bank
                    </th>
                </tr>
                </thead>
                <tbody>
                @php $sl = 0 @endphp
                @foreach($results as $item => $row)
                    @php $sl++ @endphp
                    <tr>
                        <td>{{$sl}}</td>
                        <td>@include('lot.item.action-dropdown')</td>
                        <td>{{$row->index}}</td>
                        <td>
                            @if ($row->status == 'sent')
                                <span class='badge badge-success'>Sent</span>
                            @elseif ($row->status == 'hold')
                                <span class='badge badge-warning'>Hold</span>
                            @elseif ($row->status == 'returned')
                                <span class='badge badge-danger'>Returned</span>
                            @elseif ($row->status == 'stop')
                                <span class='badge badge-danger'>Stopped</span>
                            @else
                                <span class='badge badge-primary'>Processing</span>
                            @endif
                        </td>
                        <td>{{\Carbon\Carbon::parse($row->lotDate)->format('d-m-Y')}}</td>
                        <td>{{$row->receiver_name}}</td>
                        <td>{{$row->account_no}}</td>
                        <td>{{number_format($row->amount, 2)}}</td>
                        <td>{{$row->lotName}}</td>
                        <td>{{$row->bankName}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade bd-example-modal-xl" id="viewLotItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">View Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id='lot_iteem_value'>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-xl" id="returnedLotItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Return this Index Amount</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id='return_iteem_value'>
                    <form id="return_confirmation_form" enctype="multipart/form-data">
                        <input type="hidden" name="selected" id="itmslcted"/>
                        <input type="hidden" name="type" id='itmtype'/>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {!! Form::label('with_credit', 'With Credit', ['control-label']) !!}
                                    {!! Form::select('with_credit', [1 => 'With Credit', 0 => 'Without Credit'], '', ['class' => 'form-control', 'placeholder' => 'Select', 'required' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-12">
                                {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                                {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    {!! Form::label('comment', 'Comment', ['control-label']) !!}
                                    {!! Form::textarea('comment', '', ['rows' => 4, 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {!! Form::label('file', 'Stop Documents', ['control-label']) !!}
                                    <input type="file" class="form-control" id="stop_file" name="file"
                                           accept="image/jpeg, image/jpg, image/png, application/pdf"
                                    />
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-xl" id="sentLotItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send this Index Amount</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id='sent_iteem_value'>
                    <form id="sent_confirmation_form" enctype="multipart/form-data">
                        <input type="hidden" name="selected" id="itmslctedsend"/>
                        <input type="hidden" name="type" id='itmtype'/>
                        <div class="row">
                            <div class="col-12">
                                {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                                {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {!! Form::label('file', 'Documents', ['control-label']) !!}
                                    <input type="file" class="form-control" id="stop_file" name="file"
                                           accept="image/jpeg, image/jpg, image/png, application/pdf"
                                    />
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-xl" id="stopLotItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Stop this Index Amount</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id='stop_iteem_value'>
                    <form id="stop_confirmation_form" enctype="multipart/form-data">
                        <input type="hidden" name="stopselected" id="itmslctedstop"/>
                        <input type="hidden" name="type" id='itmtype'/>
                        <div class="row">
                            <div class="col-12">
                                {!! Form::label('date', 'Stop Date', ['class' => 'control-label']) !!}
                                {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {!! Form::label('comment', 'Stop Reason', ['control-label']) !!}
                                    {!! Form::textarea('comment', '', ['rows' => 4, 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {!! Form::label('file', 'Stop Documents', ['control-label']) !!}
                                    <input type="file" class="form-control" id="stop_file" name="file"
                                           accept="image/jpeg, image/jpg, image/png, application/pdf"
                                    />
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="holdLotItemModal" tabindex="-1" aria-hidden="true">
        <x-bootstrap-modal title="Hold this Item" :hideFooter="false">
            <form id="hold_confirmation_form" enctype="multipart/form-data">
                <input type="hidden" name="selected" id="item_selected_hold"/>
                <input type="hidden" name="type" id='itmtype'/>
                <div class="row">
                    <div class="col-12">
                        {!! Form::label('date', 'Date', ['class' => 'control-label']) !!}
                        {!! Form::text('date', '', ['class' => 'form-control date-time-picker']) !!}
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            {!! Form::label('file', 'Stop Documents', ['control-label']) !!}
                            <input type="file" class="form-control" id="stop_file" name="file"
                                   accept="image/jpeg, image/jpg, image/png, application/pdf"
                            />
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </x-bootstrap-modal>
    </div>

    <div class="modal fade" id="viewLotItemModal" tabindex="-1" aria-hidden="true">
    </div>

    <div class="modal fade bd-example-modal-xl" id="edit_lot_transaction_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            $('#lotItemsTable').DataTable({});

            $('#date_range').daterangepicker({
                //startDate: '2025/09/20',
                locale: {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                }
            });
        });
        $(document).on('click', '.action-btn', function () {

            let id = $(this).data('id');
            let type = $(this).data('type');
            if (type == 'view') {

                $("#viewLotItemModal").load('/lots/search/' + id + '/get-lot-item', function () {
                    $(this).modal('show');
                })

                /*$.ajax({
                    method: 'GET',
                    url: '/lots/search/' + id + '/get-lot-item',
                    dataType: 'json',
                    success: function (data) {
                        $('#lot_iteem_value').html(data);
                        $("#viewLotItemModal").modal('show');
                    }
                });*/

            }
            if (type == 'returned') {
                $('#itmtype').val(type);
                $('#itmslcted').val(id);
                $("#returnedLotItemModal").modal('show');

            }
            if (type == 'sent') {
                $('#itmtype').val(type);
                $('#itmslctedsend').val(id);
                $("#sentLotItemModal").modal('show');

            }
            if (type == 'stop') {
                $('#itmtype').val(type);
                $('#itmslctedstop').val(id);
                $("#stopLotItemModal").modal('show');
            }

            if (type == 'hold') {
                $('#itmtype').val(type);
                $('#item_selected_hold').val(id);
                $("#holdLotItemModal").modal('show');
            }


        });


        $(document).on('submit', '#sent_confirmation_form', function (e) {
            e.preventDefault()

            console.log('submit');

            /*let formDataArray = $(this).serializeArray();
            let formDataObject = {};

            // Convert the form data array to an object
            $.each(formDataArray, function (index, field) {
                formDataObject[field.name] = field.value;
            });

            let type = $(this).find("input[name='type']").val();
            let date = $(this).find('input[name="date"]').val();*/

            let formData = new FormData(this);

            $.ajax({
                method: 'POST',
                url: window.location.pathname + '/send-index',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#sentLotItemModal").modal('hide');
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        location.reload();
                    } else {
                        toastr.error(data.message);
                    }

                }
            });
        });

        $(document).on('submit', '#hold_confirmation_form', function (e) {
            e.preventDefault()

            /*let formDataArray = $(this).serializeArray();
            let formDataObject = {};

            // Convert the form data array to an object
            $.each(formDataArray, function (index, field) {
                formDataObject[field.name] = field.value;
            });

            let type = $(this).find("input[name='type']").val();
            let date = $(this).find('input[name="date"]').val();*/

            let formData = new FormData(this);

            console.log({formData})

            //return;

            $.ajax({
                method: 'POST',
                url: '/hold-lot-item',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#holdLotItemModal").modal('hide');
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        location.reload();
                    } else {
                        toastr.error(data.message);
                    }

                }
            });
        });


        $(document).on('submit', '#return_confirmation_form', function (e) {
            e.preventDefault()


            /*let formDataArray = $(this).serializeArray();
            let formDataObject = {};

            // Convert the form data array to an object
            $.each(formDataArray, function (index, field) {
                formDataObject[field.name] = field.value;
            });

            let id = $('#itmslcted').val();

            let type = $(this).find("input[name='type']").val();
            let withCredit = $(this).find("input[name='with_credit']").val();*/

            let data = new FormData(this);

            $.ajax({
                method: 'POST',
                url: window.location.origin + '/return-index',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#returnedLotItemModal").modal('hide');
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        location.reload();
                    } else {
                        toastr.error(data.message);
                    }

                }
            });
        });

        $(document).on('submit', '#stop_confirmation_form', function (e) {
            e.preventDefault()

            let formData = new FormData(this);

            console.log('stop', formData)

            $.ajax({
                method: 'POST',
                url: window.location.origin + '/stop-index',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    $("#stopLotItemModal").modal('hide');
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        location.reload();
                    } else {
                        toastr.error(data.message);
                    }

                }
            });
        });

        $(document).on('click', '.edit-transaction-btn', function () {
            let url = $(this).data('href');
            $('#edit_lot_transaction_modal').load(url, function () {
                $(this).modal("show");
                datePicker();
            })
        })

        $(document).on('submit', '#transaction_edit_form', function (event) {
            event.preventDefault();
            let url = $(this).attr('action');
            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        //close this modal
                        $('#edit_lot_transaction_modal').modal('hide');
                        //show view modal
                        $('#viewLotItemModal').load(data.reload_url, function () {
                            $(this).modal('show');
                        })
                    } else {
                        toastr.error(data.message);
                    }
                }
            })
        })

    </script>
@endpush
