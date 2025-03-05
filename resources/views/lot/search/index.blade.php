@extends('layouts.app')
@push('css')
<link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush
@section('main')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <h4>Lot Search</h4>
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
@endsection
@push('scripts')
<script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script>
$(document).ready(function () {

    $('#date_range').daterangepicker({
//startDate: '2025/09/20',
        locale: {
            format: 'YYYY-MM-DD',
            separator: '~',
        }
    });

    $('#date_range').on('change', function () {
        table.ajax.reload();
    })

});

</script>
@endpush

