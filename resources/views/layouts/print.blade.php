<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>হিসাব বিবরণী</title>
    <style>
        .bn-font {
            font-family: 'solaimanlipi', sans-serif;
        }
        .en-font{
            font-family: sans-serif;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .table-bordered, td, th {
            border: 1px solid #939393;
            padding: 7px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .text-right {
            text-align: right;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        th {
            background-color: #333333;
            color: #FFF;
            font-size: 1.3em;
            text-align: center;
        }

        .head_title {
            font-size: 1.6em;
            text-align: center;
        }

        .head_txt {
            font-size: 1.5em;
            text-align: center;
        }

        .footer_name {
            font-size: 1.2em;
            text-align: center;
        }

        td {
            font-size: 1.2em;
        }

        .head_body {
            font-size: 1.3em;
            text-align: center;
        }

        .bg-white{
            background-color: #fff;
            color: black;
        }

    </style>
    @yield('css')
</head>
<body>

@yield('content')

<script type="text/php">
        if ( isset($pdf) ) {
        $pdf->page_script('
        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
        $size = 12;
        $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
        $y = 15;
        $x = 520;
        $pdf->text($x, $y, $pageText, $font, $size);
        ');
        }

</script>

</body>
</html>
