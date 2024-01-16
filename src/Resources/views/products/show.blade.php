@extends('isotope::master')

@section('title', 'Product Details')

@push('buttons')
<a href="{{ route('products.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
    {{ __('Product List') }}
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
                            <th>{{ __('Code') }}</th>
                            <td>{{ $product->product_code }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <td>{{ $product->product_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Category') }}</th>
                            <td>{{ $product->category->category_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Cost') }}</th>
                            <td>{{ format_currency($product->product_cost) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Price') }}</th>
                            <td>{{ format_currency($product->product_price) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Stock Worth') }}</th>
                            <td>
                                {{ __('COST') }}:: {{ format_currency($product->product_cost) }} /
                                {{ __('PRICE') }}:: {{ format_currency($product->product_price) }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Alert Quantity') }}</th>
                            <td>{{ $product->product_stock_alert }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Note') }}</th>
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



