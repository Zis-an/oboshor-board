<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{config('app.name')}}</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"/>
    <!--bootstrap-->
    <link rel="stylesheet" href="{{asset('adminLTE/plugins/sweetalert2/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminLTE/plugins/toastr/toastr.css')}}">
    <link rel="stylesheet" href="{{asset('adminLTE/plugins/select2/css/select2.css')}}">

    <link rel="stylesheet" href="{{asset('adminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">

    <link rel="stylesheet"
          href="{{asset('adminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}"/>

   {{-- <link rel="stylesheet" href="{{asset('adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}"/>--}}

    <link rel="stylesheet" href="{{asset('adminLTE/dist/css/adminlte.min.css')}}">

    <link rel="stylesheet" href="{{asset('adminLTE/dist/css/alt/adminlte.components.css')}}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{asset('css/app.css')}}">

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker-standalone.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.jqueryui.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.jqueryui.min.css"/>
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>


    @stack('css')

</head>
<body>
<div class="wrapper">
    <div>
        @include('layouts.partials.sidebar')
        @include('layouts.partials.header')
    </div>

    <div class="content-wrapper px-2">
        @yield('main')
    </div>
</div>

<script src="{{asset('adminLTE/plugins/jquery/jquery.min.js')}}"></script>
<!-- popper js -->
<script src="{{asset('adminLTE/plugins/popper/umd/popper.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Select 2 -->
<script src="{{asset('adminLTE/plugins/select2/js/select2.js')}}"></script>

<script src="{{asset('adminLTE/plugins/toastr/toastr.min.js')}}"></script>

<script src="{{asset('adminLTE/plugins/sweetalert2/sweetalert2.min.js')}}"></script>

<script src="{{asset('adminLTE/plugins/jquery-validation/jquery.validate.min.js')}}"></script>

<!-- data table -->
<script src="{{asset('adminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>

<script src="{{asset('adminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('adminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('adminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

<script src="{{asset('adminLTE/plugins/moment/moment.min.js')}}"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('adminLTE/dist/js/adminlte.min.js')}}"></script>

<script src="{{asset('js/app.js')}}"></script>

<script type="text/javascript">
    $('select').select2({});
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('ul').Treeview();

    //add additional validation
    jQuery.validator.addMethod("phoneBD", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^([+]{1}[8]{2}|0088)?(01){1}[3-9]{1}\d{8}$/)
    }, "Please specify a valid phone number");

</script>
@livewireScripts
@stack('scripts')
</body>
</html>
