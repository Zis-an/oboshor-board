@extends('layouts.app')
@push('css')
    <link rel="stylesheet" href="{{asset('/adminLTE/plugins/daterangepicker/daterangepicker.css')}}"/>
@endpush


@section('main')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>STD Account Sub Report</h3><br />
        <h3>{{$banks->account_no}}</h3><br />
        <h3>{{$banks->br_name}}, {{$banks->short}}</h3>
<!--        <div>
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>-->
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table" id="account_book_table">
                <thead>
                    <tr>
                        <th>ক্রমিক নং</th>
                        <th>তারিখ</th>
                        <th>বিবরণ</th>
                        <th>মোট জমার পরিমাণ</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($accounts) && !empty($accounts))
                    @foreach($accounts as $key => $account)
                    <tr>
                        <td>{{ $loop->iteration }}</td> 
                        <td>{{ $account->date ?? '' }}</td>                        
                        <td>{{ $account->description ?? '' }}</td>
                        <td class="text-right">{{ number_format($account->amount ?? 0, 2) }}</td>
                    </tr>

                    <!-- Modal Structure -->
                    <!-- Add modal content here if it depends on the $account -->
                    @endforeach
                    @endif

                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold; text-align: right;">
                            সর্বমোট
                        </td>
                        <td  style="font-weight: bold; text-align: right;">{{number_format($accounts->sum('amount'),2)}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection