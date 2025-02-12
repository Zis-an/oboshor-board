@extends('layouts.app')

@section('main')

    <div class="col-sm-4 my-2">
        <h4>Financial Year: {{$financialYear->name}}</h4>
    </div>

    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Banks</span>
                        <h4 class="info-box-number">{{$bankCount}}</h4>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total FDR Account</span>
                        <h4 class="info-box-number">{{$fdrAccountCount}}</h4>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total STD Account</span>
                        <h4 class="info-box-number">{{$stdAccountCount}}</h4>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total FDR Amount</span>
                        <h4 class="info-box-number">{{number_format($fdrAmount, 2)}}</h4>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total STD Amount</span>
                        <h4 class="info-box-number">{{number_format($stdAmount, 2)}}</h4>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Expense Budget</span>
                        <h4 class="info-box-number">{{number_format($totalExpenseBudget, 2)}}</h4>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Income Budget</span>
                        <h4 class="info-box-number">{{number_format($totalIncomeBudget, 2)}}</h4>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
