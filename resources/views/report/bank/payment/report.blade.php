@extends('layouts.app')
@section('main')
<div class="card">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-sm-12"><h2>Payments Report</h2></div>
            <div class="col-sm-4">
                {{Form::label('account_id', 'Account')}}
                {!! Form::select('account_id', $accounts, '', ['class' => 'form-control select2-search', 'placeholder' => 'All Accounts']) !!}
            </div>
            <div class="col-sm-4">
                {{Form::label('date', 'Date Range')}}
                {!! Form::text('date', $financialYearRange, ['class' => 'form-control']) !!}
            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary" id="filter_btn">
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
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        locale: {
            format: 'YYYY-MM-DD',
            separator: '~',
        }
    });


    $('#filter_btn').on('click', function () {

        let account = $('#account_id').val();
        let date_range = $('#date').val();

        $.ajax({
            url: window.location.path,
            data: {
                account_id: account,
                date: date_range,
            },
            success: function (html) {
                $('#cashbook_report_container').html(html);
                $('#lot_hold_table').DataTable({})
            }
        })


    })

    $(document).on('click', '#export_btn_pdf', function () {

        let account = $('#account_id').val();
        let date = $('#date').val();

        let url = `/payment-items-report-export?type=pdf&account=${account}&date=${date}`

        window.open(url, '_blank');

    })

    $(document).on('click', '#export_btn_excel', function () {

        let account = $('#account_id').val();
        let date = $('#date').val();

        let url = '/payment-items-report-export?type=excel&account=' + account + '&date='+ date;

        window.open(url, '_blank');

    })

})

</script>

@endpush
