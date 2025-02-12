@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="employee_payments_table">
                <thead>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('#employee_payments_table').DataTable({
                ajax: '/employee-payment-report'
            })
        })
    </script>
@endpush
