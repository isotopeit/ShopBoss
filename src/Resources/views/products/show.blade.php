@extends('isotope::master')

@section('title', __('shopboss::shopboss.productDetails'))

@push('buttons')
<a href="{{ route('products.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
    {{ __('shopboss::shopboss.productList') }}
</a>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-9">
        <div class="card h-100">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <tr>
                            <th>{{ __('shopboss::shopboss.code') }}</th>
                            <td>{{ $product->product_code }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('shopboss::shopboss.name') }}</th>
                            <td>{{ $product->product_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('shopboss::shopboss.category') }}</th>
                            <td>{{ $product->category->category_name }}</td>
                        </tr>
                        @if (settings()->enable_branch == 1)
                        <tr>
                            <th>{{ __('shopboss::shopboss.branch') }}</th>
                            <td>{{ $product->branch->name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>{{ __('shopboss::shopboss.cost') }}</th>
                            <td>{{ format_currency($product->product_cost) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('shopboss::shopboss.price') }}</th>
                            <td>{{ format_currency($product->product_price) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('shopboss::shopboss.stockWorth') }}</th>
                            <td>
                                {{ __('shopboss::shopboss.cost') }}:: {{ format_currency($product->product_cost) }} /
                                {{ __('shopboss::shopboss.price') }}:: {{ format_currency($product->product_price) }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('shopboss::shopboss.alertQuantity') }}</th>
                            <td>{{ $product->product_stock_alert }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('shopboss::shopboss.note') }}</th>
                            <td>{{ $product->product_note ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                @foreach($product->uploads as $upload)
                    <img src="{{ $upload->folder.$upload->filename }}" alt="Product Image" class="img-fluid img-thumbnail mb-2">
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection



