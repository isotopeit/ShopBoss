@extends('isotope::master')

@section('title', __('shopboss::shopboss.products'))

@push('buttons')
    <a href="{{ route('products.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('shopboss::shopboss.addProduct') }}
    </a>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="row mb-3">
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['category_name'] ?? '' }}" class="form-control form-control-sm" name="search[category_name]" placeholder="{{ __('shopboss::shopboss.enterCategoryName') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['product_name'] ?? '' }}" class="form-control form-control-sm" name="search[product_name]" placeholder="{{ __('shopboss::shopboss.enterProductName') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['product_code'] ?? '' }}" class="form-control form-control-sm" name="search[product_code]" placeholder="{{ __('shopboss::shopboss.enterProductCode') }}">
                    </div>
                    @if (settings()->enable_branch == 1)
                    <div class="col-md">
                        <select name="search[branch_id]" class="form-select form-select-sm" data-control="select2" data-placeholder="{{ __('shopboss::shopboss.selectBranch') }}">
                            <option value="">{{ __('shopboss::shopboss.allBranches') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ (Request::input('search')['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md">
                        <button type="submit" class="btn btn-sm bg-isotope text-white"><i class="fa-solid fa-search text-white"></i> {{ __('shopboss::shopboss.search') }}</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead class="bg-isotope">
                            <tr>
                                <td>{{ __('shopboss::shopboss.slNo') }}</td>
                                <td>{{ __('shopboss::shopboss.category') }}</td>
                                <td>{{ __('shopboss::shopboss.name') }}</td>
                                <td>{{ __('shopboss::shopboss.code') }}</td>
                                <td>{{ __('shopboss::shopboss.cost') }}</td>
                                <td>{{ __('shopboss::shopboss.price') }}</td>
                                <td>{{ __('shopboss::shopboss.unit') }}</td>
                                <td>{{ __('shopboss::shopboss.stockAlert') }}</td>
                                @if (settings()->enable_branch == 1)
                                <td>{{ __('shopboss::shopboss.branch') }}</td>
                                @endif
                                <td class="text-center">{{ __('shopboss::shopboss.actions') }}</td>
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
                                @if (settings()->enable_branch == 1)
                                <td>{{ $product->branch->name ?? 'N/A' }}</td>
                                @endif
                                <td class="d-flex justify-content-center">
                                    <a title="{{ __('shopboss::shopboss.show') }}"
                                        class="btn btn-outline btn-outline-dashed btn-outline-primary p-0 me-1"
                                        href="{{ route('products.show', $product->uuid) }}">
                                        <i class="fas fa-eye ms-1"></i>
                                    </a>
                                    <a title="{{ __('shopboss::shopboss.edit') }}"
                                        class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                        href="{{ route('products.edit', $product->uuid) }}">
                                        <i class="fas fa-edit ms-1"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->uuid) }}" method="post">
                                        @method('delete') @csrf
                                        <button title="{{ __('shopboss::shopboss.delete') }}" type="submit" onclick="return confirm('{{ __('shopboss::shopboss.deleteConfirm') }}')"
                                            class="btn btn-outline btn-outline-dashed btn-outline-danger p-0 me-1">
                                            <i class="fa-solid fa-trash ms-1"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <th class="font-weight-bold text-center text-danger" colspan="{{ settings()->enable_branch == 1 ? '10' : '9' }}">{{ __('shopboss::shopboss.noDataFound') }}!</th>
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

