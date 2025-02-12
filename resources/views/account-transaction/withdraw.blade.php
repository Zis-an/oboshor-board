@extends('layouts.app')

@section('main')
    <div class="card">
        <div class="card-body">
            @include('account-transaction.partials.withdraw-form', ['account' => $account])
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            $('#account_id').select2({});

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

        })
    </script>
@endpush
