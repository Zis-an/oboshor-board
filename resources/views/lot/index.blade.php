@extends('layouts.app')

@section('main')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <h4>Lots</h4>
            <a href="{{route('lots.create')}}" class="btn btn-primary create-modal-open-btn">
                <i class="fa fa-plus"></i>
                Create</a>
        </div>
    </div>
</section>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-4">
                {!!  Form::label("account_id", "Bank Account")!!}
                {!! Form::select('account_id', $accounts, '', ['class' => 'select2 form-control', 'placeholder' => 'All']) !!}
            </div>
        </div>
    </div>
</div>

<div class="card">

    <div class="card-header">
        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
    </div>

    <div class="card-body">
        <table class="w-100 table" id="expenseHeadTable">
            <thead>
                <tr>
                    <th>Lot Name</th>
                    <th>Approved Date</th>
                    <th>Total Amount</th>
                    <th>Total</th>
                    <th>Sent</th>
                    <th>Hold</th>
                    <th>Returned</th>
                    <th>Processing</th>
                    <th>Bank</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

<div class="modal fade" id="payLotModal" tabindex="-1" aria-hidden="true">
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/plug-ins/1.10.21/dataRender/datetime.js" charset="utf8"></script>

<script>
    $(document).ready(function () {

        $('#account_id').select2({})

        let table = $('#expenseHeadTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '/lots',
                data: function (d) {
                    d.account_id = $("#account_id").val()
                }
            },
            columnDefs: [
                {
                    targets: '_all',
                    orderable: false,
                    searchable: false,
                },
            ],
            columns: [
                {data: 'short_name', searchable: true},
                {data: 'approve_date', type: 'date',
                    render:  $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY')},
                {data: 'total_amount'},
                {data: 'total'},
                {data: "sent_count"},
                {data: 'hold_count'},
                {data: 'returned_count'},
                {data: 'processing_count'},
                {data: 'bank_Name'},
                {data: 'actions'}
            ],
//            columnDefs: [{
//                    targets: 1,
//                    render: $.fn.dataTable.render.moment('Do MMM YYYY')
//                }]
        });




        //on click edit unit button

        $(document).on('click', '.delete-lot-btn', function () {

            let url = $(this).data('href');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'delete',
                        dataType: 'json',
                        success: function (res) {
                            console.log("deleted", res);
                            toastr.success("Item deleted");
                            table.ajax.reload();
                        },
                        error: function (er) {
                            console.log(er)
                        }
                    });

                }
            })

        })

        //pay button
        $(document).on('click', '.pay-lot-btn', function () {
            $('#payLotModal').load($(this).data('href'), function (result) {
                $(this).modal('show');
            })
        })

        $(document).on('change', '#lotBankSelect', function () {
            let bankId = this.value;

            $.ajax({
                url: `/get-accounts-data?bank=${bankId}`,
                success: function (data) {

                    // Transforms the top-level key of the response object from 'items' to 'results'
                    let options = '<option>Select Account</option>';
                    data.map(item => {
                        options += `<option value='${item.id}'>${item.name}</option>`
                    })
                    $('#lotSelectAccount').html(options);

                },
                error: function () {

                }
            })
        })

        //on account select

        $(document).on('change', '#lotSelectAccount', function () {
            let accountId = this.value;

            $.ajax({
                url: `/get-account-info?account=${accountId}`,
                success: function (data) {
                    $('#accountBalance').text("Account Balance: " + data.balance)
                }
            })
        })

        //create payment

        $(document).on('submit', 'form#createLotPayment', function (e) {
            e.preventDefault();

            $(this)
                    .find('button[type="submit"]')
                    .attr('disabled', true);
            //use form data instead of serialize in file upload
            const data = new FormData(this);

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                processData: false,
                contentType: false,
                success: function (response) {

                    let {status, message} = response;

                    if (status === 'success') {
                        toastr.success(message);
                        table.ajax.reload();
                    } else {
                        toastr.error(message);
                    }

                    $('#payLotModal').modal('hide');

                },
            });
        });

        $('#account_id').on('change', function () {
            table.ajax.reload();
        })

    })

    $(document).on('click', '#export_btn_pdf', function () {

        let account_id = $('#account_id').val();

        let url = window.location.pathname + '-export' + `?type=pdf&account_id=${account_id}`;

        window.open(url, '_blank');

    })

    $(document).on('click', '#export_btn_excel', function () {

        let account_id = $('#account_id').val();

        let url = window.location.pathname + '-export' + `?type=excel&account_id=${account_id}`;

        window.open(url, '_blank');

    })

</script>
@endpush
