@extends('layouts.print')

@section('content')
    <div>
        <div class="card-body">
            <div class="bn-font head_title">বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড</div>
            <div class="bn-font head_txt">শিক্ষা মন্ত্রণালয়</div>
            <div class="bn-font head_title">লট উয়াইজ বিবরণী</div>
            <div class="head_title"><span class="bn-font">লটের নামঃ</span> {{$lot->name}}</div>

            <div style="margin: 5px 0">
                <table class="table">
                    <tr class="bg-white">
                        <th class="bg-white">Total</th>
                        <td>{{$total_count}}</td>
                        <th class="bg-white">Processing</th>
                        <td>{{$processing_count}}</td>
                        <th class="bg-white">Sent</th>
                        <td>{{$sent_count}}</td>
                    </tr>
                    <tr class="bg-white">
                        <th class="bg-white">Hold</th>
                        <td class="bg-white">{{$hold_count}}</td>
                        <th class="bg-white">Stop</th>
                        <td class="bg-white">{{$stop_count}}</td>
                        <th class="bg-white">Returned</th>
                        <td class="bg-white">{{$returned_count}}</td>
                    </tr>
                </table>
            </div>

        </div>

        <table>
            <thead>
            <tr>
                <th class="bn-font">ক্রমিক নং</th>
                <th class="bn-font">ইন্ডেক্স</th>
                <th class="bn-font">গ্রাহকের নাম</th>
                <th class="bn-font">ব্যাংক একাউন্ট</th>
                <th class="bn-font">ব্রাঞ্চ</th>
                <th class="bn-font">পরিমাণ</th>
                <th class="bn-font">স্ট্যাটাস</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $index=>$item)
                <tr>
                    <td>{{$index + 1}}</td>
                    <td>{{$item->index}}</td>
                    <td>{{$item->receiver_name}}</td>
                    <td>{{$item->account_no}}</td>
                    <td>{{$item->branch_name}}</td>
                    <td>{{$item->amount}}</td>
                    <td>{{$item->status}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
@endsection
