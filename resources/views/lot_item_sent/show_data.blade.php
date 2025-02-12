@extends('layouts.app')

@section('main')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <h4>Index Report</h4>
        </div>
    </div>
</section>

@include('partials.error-alert', ['errors' => $errors])

<section class="card">

    <div class="card-body">
        <div class="row"> 
            <div class="col-12">
                <p><strong>Not Uploaded</strong></p>
                @if(isset($not_upload) && !empty($not_upload))

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>SL</th>
                        <th>Index</th>
                        <th>Excel Amount</th>
                        <th>Updated Amount</th>
                        <th>Transaction Date</th>
                    </tr>
                    @foreach($not_upload as $key => $val)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$val[0]}}</td>
                        <td>{{$val[1]}}</td>
                        <td>{{$val[2]}}</td>
                        <td>{{$val[3]}}</td>
                    </tr>
                    @endforeach
                </table>
                @endif
                <p><strong>Not Save now</strong></p>
                @if(isset($not_upload_now) && !empty($not_upload_now))

                <table class="table table-striped table-bordered">
                    <tr>
                        <th>SL</th>
                        <th>Index</th>
                        <th>Excel Amount</th>
                        <th>Updated Amount</th>
                        <th>Transaction Date</th>
                    </tr>
                    @foreach($not_upload_now as $key => $val)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$val[0]}}</td>
                        <td>{{$val[1]}}</td>
                        <td>{{$val[2]}}</td>
                        <td>{{$val[3]}}</td>
                    </tr>
                    @endforeach
                </table>
                @endif

                <p><strong>Already Sent</strong></p>
                @if(isset($uplod_tran) && !empty($uplod_tran))
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>SL</th>
                        <th>Index</th>
                        <th>Excel Amount</th>
                        <th>Updated Amount</th>
                        <th>Transaction Date</th>
                    </tr>
                    @foreach($uplod_tran as $key => $val)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$val[0]}}</td>
                        <td>{{$val[1]}}</td>
                        <td>{{$val[2]}}</td>
                        <td>{{$val[3]}}</td>
                    </tr>
                    @endforeach
                </table>
                @endif
                <p><strong>Now Sent</strong></p>
                @if(isset($not_tran) && !empty($not_tran))
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>SL</th>
                        <th>Index</th>
                        <th>Excel Amount</th>
                        <th>Updated Amount</th>
                        <th>Transaction Date</th>
                    </tr>

                    @foreach($not_tran as $key => $val)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$val[0]}}</td>
                        <td>{{$val[1]}}</td>
                        <td>{{$val[2]}}</td>
                        <td>{{$val[3]}}</td>
                    </tr>
                    @endforeach
                </table>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
