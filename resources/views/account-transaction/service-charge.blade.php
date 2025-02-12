@extends('layouts.app')


@section('main')
    <div class="card">
        <div class="card-body">
            @include('account-transaction.partials.service-charge-form', ['accounts' => $accounts, 'account' => $account])
        </div>
    </div>

@endsection
