@extends('isotope::master')

@section('title', 'Home')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                @can('show_total_stats')
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0">
                            <div class="card-body p-0 d-flex align-items-center shadow-sm">
                                <div class="bg-gradient-primary p-4 mfe-3 rounded-left">
                                    <i class="bi bi-bar-chart font-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-value text-primary">{{ $revenue }}</div>
                                    <div class="text-muted text-uppercase font-weight-bold small">{{ __('Revenue') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0">
                            <div class="card-body p-0 d-flex align-items-center shadow-sm">
                                <div class="bg-gradient-warning p-4 mfe-3 rounded-left">
                                    <i class="bi bi-arrow-return-left font-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-value text-warning">{{ $sale_returns }}</div>
                                    <div class="text-muted text-uppercase font-weight-bold small">{{ __('Sales Return') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0">
                            <div class="card-body p-0 d-flex align-items-center shadow-sm">
                                <div class="bg-gradient-success p-4 mfe-3 rounded-left">
                                    <i class="bi bi-arrow-return-right font-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-value text-success">{{ $purchase_returns }}</div>
                                    <div class="text-muted text-uppercase font-weight-bold small">{{ __('Purchases Return') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0">
                            <div class="card-body p-0 d-flex align-items-center shadow-sm">
                                <div class="bg-gradient-info p-4 mfe-3 rounded-left">
                                    <i class="bi bi-trophy font-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-value text-info">{{ $profit }}</div>
                                    <div class="text-muted text-uppercase font-weight-bold small">{{ __('All Profit') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            </div>
        </div>


        <div class="row mb-4">
            @can('show_weekly_sales_purchases')
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header">
                            {{ __('Sales & Purchases of Last 7 Days') }}
                        </div>
                        <div class="card-body">
                            <canvas id="salesPurchasesChart"></canvas>
                        </div>
                    </div>
                </div>
            @endcan
            @can('show_month_overview')
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header">
                            {{ __('Overview of ' . now()->format('F')) }}, {{ now()->format('Y') }}
                        </div>
                        <div class="card-body d-flex justify-content-center">
                            <div class="chart-container" style="position: relative; height:auto; width:280px">
                                <canvas id="currentMonthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        @can('show_monthly_cashflow')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            {{ __('Monthly Cash Flow (Payment Sent & Received)') }}
                        </div>
                        <div class="card-body">
                            <canvas id="paymentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection