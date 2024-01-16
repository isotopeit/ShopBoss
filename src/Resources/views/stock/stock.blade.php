@extends('isotope::master')

@section('title', 'Stock Status')

@section('content')
<div class="card">
    <div class="card-body">
        <form class="row mb-3">
            <div class="col-md">
                <input type="text" value="{{ Request::input('product_code') ?? '' }}"
                    class="form-control form-control-sm" name="product_code"
                    placeholder="{{ __('Enter Product Code') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('product_name') ?? '' }}"
                    class="form-control form-control-sm" name="product_name"
                    placeholder="{{ __('Enter Product Name') }}">
            </div>
            <div class="col-md">
                <button type="submit" class="btn btn-sm bg-isotope text-white"><i
                        class="fa-solid fa-search text-white"></i> {{ __('Search') }}</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped h-100">
                <thead class="bg-isotope">
                    <tr>
                        <td>{{ __('#SL') }}</td>
                        <td>{{ __('Product Code') }}</td>
                        <td>{{ __('Product Name') }}</td>
                        <td>{{ __('Unit Price') }}</td>
                        <td>{{ __('Stock Qty') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $item->stock_qty }}</td>
                    </tr>
                    @empty
                        <tr>
                            <th class="text-center text-danger" colspan="5">{{ __('No Data Found!') }}</th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
