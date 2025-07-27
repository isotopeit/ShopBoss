@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.adjustmentDetails'))

@push('page_css')
    @livewireStyles
@endpush

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">{{ __('shopboss::shopboss.adjustments') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.details') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2">
                                        {{ __('shopboss::shopboss.date') }}
                                    </th>
                                    <th colspan="2">
                                        {{ __('shopboss::shopboss.reference') }}
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        {{ $adjustment->date }}
                                    </td>
                                    <td colspan="2">
                                        {{ $adjustment->reference }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{ __('shopboss::shopboss.productName') }}</th>
                                    <th>{{ __('shopboss::shopboss.code') }}</th>
                                    <th>{{ __('shopboss::shopboss.quantity') }}</th>
                                    <th>{{ __('shopboss::shopboss.type') }}</th>
                                </tr>

                                @foreach($adjustment->adjustedProducts as $adjustedProduct)
                                    <tr>
                                        <td>{{ $adjustedProduct->product->product_name }}</td>
                                        <td>{{ $adjustedProduct->product->product_code }}</td>
                                        <td>{{ $adjustedProduct->quantity }}</td>
                                        <td>
                                            @if($adjustedProduct->type == 'add')
                                                (+) {{ __('shopboss::shopboss.addition') }}
                                            @else
                                                (-) {{ __('shopboss::shopboss.subtraction') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
