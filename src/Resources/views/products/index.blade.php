@extends('isotope::master')

@section('title', 'Products')

@push('buttons')
    <a href="{{ route('products.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('Add Product') }}
    </a>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="row mb-3">
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['category_name'] ?? '' }}" class="form-control form-control-sm" name="search[category_name]" placeholder="{{ __('Enter Category Name') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['product_name'] ?? '' }}" class="form-control form-control-sm" name="search[product_name]" placeholder="{{ __('Enter Product Name') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['product_code'] ?? '' }}" class="form-control form-control-sm" name="search[product_code]" placeholder="{{ __('Enter Product Code') }}">
                    </div>
                    <div class="col-md">
                        <button type="submit" class="btn btn-sm bg-isotope text-white"><i class="fa-solid fa-search text-white"></i> Search</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="bg-isotope">
                            <tr>
                                <td>{{ __('#SL') }}</td>
                                <td>{{ __('Category') }}</td>
                                <td>{{ __('Name') }}</td>
                                <td>{{ __('Code') }}</td>
                                <td>{{ __('Cost') }}</td>
                                <td>{{ __('Price') }}</td>
                                <td>{{ __('Unit') }}</td>
                                <td>{{ __('Stock Alert') }}</td>
                                <td class="text-center">{{ __('Actions') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->category->category_name }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->product_cost }}</td>
                                <td>{{ $product->product_price }}</td>
                                <td>{{ $product->uom }}</td>
                                <td>{{ $product->product_stock_alert }}</td>
                                <td class="d-flex justify-content-center">
                                    <a title="Show"
                                        class="btn btn-outline btn-outline-dashed btn-outline-primary p-0 me-1"
                                        href="{{ route('products.show', $product->uuid) }}">
                                        <i class="fas fa-eye ms-1"></i>
                                    </a>
                                    <a title="Edit"
                                        class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                        href="{{ route('products.edit', $product->uuid) }}">
                                        <i class="fas fa-edit ms-1"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->uuid) }}" method="post">
                                        @method('delete') @csrf
                                        <button title="Delete" type="submit" onclick="return confirm('{{ __('Are You Want To Delete This?') }}')"
                                            class="btn btn-outline btn-outline-dashed btn-outline-danger p-0 me-1">
                                            <i class="fa-solid fa-trash ms-1"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <th class="font-weight-bold text-center text-danger" colspan="9">No Data Found!</th>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

