@extends('layouts.app')

@section('main')
    <div class="card">
        <div class="card-body">
            @include('account-transaction.partials.deposit-form', ['accounts' => $accounts, 'banks' => $banks,
            'transactionMethods' => $transactionMethods, 'account' => $account, 'incomeHeads' => $incomeHeads])
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            $('#account_id').select2({});

            $('#from_account_id').select2({
                width: '100%'
            });

            $(document).on('change', '#transaction_method', function () {

                let method = this.value;

                $('.transaction-beftn').addClass('d-none');

                $('.transaction-pay-order').addClass('d-none');

                $('.transaction-cheque').addClass('d-none');

                $('#select_expense_bank').removeAttr('disabled')

                $('#select_expense_account').removeAttr('disabled');

                if (method === 'cheque') {
                    $('.transaction-cheque').removeClass('d-none')
                } else if (method === 'beftn') {
                    $('.transaction-beftn').removeClass('d-none')
                } else if (method === 'pay-order') {
                    $('.transaction-pay-order').removeClass('d-none')
                } else if (method === 'cash') {
                    $('#select_expense_bank').attr('disabled', true)
                    $('#select_expense_account').attr('disabled', true);
                }

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
