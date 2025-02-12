@extends('layouts.app')

@section('main')
    <div class="card">
        <div class="card-body">

            <h3 class="text-center text-capitalize">{{$account->name}}</h3>
            <h5 class="text-capitalize text-center">{{$account->bank->name}}</h5>
            <h6 class="text-center text-capitalize">{{$account->branch->name}}</h6>
            <table class="table w-100">
                <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Current Balance</th>
                    <th class="text-right">Interest</th>
                    <th class="text-right">Cumulative Interest</th>
                </tr>
                </thead>
                <tbody>
                @foreach($interests as $interest)
                    <tr>
                        <td>{{$interest['date']}}</td>
                        <td class="text-right">{{number_format($interest['balance'], 2)}}</td>
                        <td class="text-right">{{number_format($interest['interest'], 2)}}</td>
                        <td class="text-right">{{number_format($interest['cumulative_interest'], 2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    </div>
@endsection
