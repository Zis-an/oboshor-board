@extends('layouts.app')

@section('main')

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4>Received Cheques</h4>
        <a class="btn btn-primary" href="{{route('cheques.create', ['type' => 'rcv'])}}">Add Receive Cheque</a>
    </div>

    <div class="card">
        <div class="card-body">

            <div>
                <table class="table table-bordered" id="cheque_table">
                    <thead>
                    <tr>
                        <th>Cheque Number</th>
                        <th>Cheque Date</th>
                        <th>Transaction Date</th>
                        <th>Amount</th>
                        <th>Received From</th>
                        <th>Deposit To</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>

    <x-modal-fade id="deposit_modal"></x-modal-fade>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
           let table =  $('#cheque_table').DataTable({
               aaSorting: [],
                ajax: window.location.pathname,
                columns: [
                    {data: 'number'},
                    {data: 'issue_date'},
                    {data: 'transaction_date'},
                    {data: 'amount'},
                    {data: 'received_from'},
                    {data: 'deposit_to'},
                    {data: 'status'},
                    {data: 'actions'}
                ]
            })

            //

            // $(document).on('click', '.deposit-btn', function(){
            //     $('#deposit_modal').load($(this).data('href'), function (){
            //         $(this).modal('show');
            //         datePicker();
            //     })
            // })

            $(document).on('click', '.deposit-btn', function () {
                $('#deposit_modal').load($(this).data('href'), function () {
                    $(this).modal('show');
                    datePicker();

                    $('#deposit_modal').on('shown.bs.modal', function () {
                        $('#account_id').select2({
                            width: '100%',
                            placeholder: 'Select Account',
                            dropdownParent: $('#deposit_modal'), // Ensure dropdown is appended to the modal
                        });
                    });
                });
            });

            $(document).on('click', '.complete-transaction-btn', function(){
                $('#deposit_modal').load($(this).data('href'), function (){
                    $(this).modal('show');
                    datePicker();
                })
            })

            //deposit modal section

            $(document).on('click', '#transaction_checkbox', function (){
                console.log('checked');
                if($(this).prop('checked')){
                    $('#transaction_date').removeClass('d-none');
                }else{
                    $('#transaction_date').addClass('d-none');
                }
            })

            $(document).on('submit','form#cheque_deposit_form, #cheque_transaction_form', function(e){
                e.preventDefault();
                submitAjaxForm(this, () => {
                    table.ajax.reload();
                    $(this).closest('.modal.fade').modal('hide');
                })
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

        })
    </script>
@endpush
