@extends('isotope::master')

@section('title', __('shopboss::shopboss.purchaseList'))

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('purchases.create') }}">{{ __('shopboss::shopboss.create') }}</a>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="row mb-1">
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['reference'] ?? '' }}"
                            class="form-control form-control-sm" name="search[reference]"
                            placeholder="{{ __('shopboss::shopboss.enterReference') }}">
                    </div>
                    <div class="col-md">
                        <input type="text" value="{{ Request::input('search')['supplier_name'] ?? '' }}"
                            class="form-control form-control-sm" name="search[supplier_name]"
                            placeholder="{{ __('shopboss::shopboss.enterSupplierName') }}">
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
                        <button type="submit" class="btn btn-sm bg-isotope text-white"><i
                                class="fa-solid fa-search text-white"></i> {{ __('Search') }}</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped h-100">
                        <thead class="bg-isotope">
                            <tr>
                                <td>{{ __('shopboss::shopboss.slNo') }}</td>
                                <td>{{ __('shopboss::shopboss.reference') }}</td>
                                <td>{{ __('shopboss::shopboss.supplier') }}</td>
                                <td>{{ __('shopboss::shopboss.totalAmount') }}</td>
                                <td>{{ __('shopboss::shopboss.paidAmount') }}</td>
                                <td>{{ __('shopboss::shopboss.dueAmount') }}</td>
                                <td>{{ __('shopboss::shopboss.paymentStatus') }}</td>
                                @if (settings()->enable_branch == 1)
                                <td>{{ __('shopboss::shopboss.branch') }}</td>
                                @endif
                                <td class="text-center">{{ __('shopboss::shopboss.actions') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $purchase->reference }}</td>
                                <td>{{ $purchase->supplier_name }}</td>
                                <td>{{ $purchase->total_amount }}</td>
                                <td>{{ $purchase->paid_amount }}</td>
                                <td>{{ $purchase->due_amount }}</td>
                                <td>{{ $purchase->payment_status }}</td>
                                @if (settings()->enable_branch == 1)
                                <td>{{ $purchase->branch->name ?? 'N/A' }}</td>
                                @endif
                                <td class="d-flex justify-content-center">
                                    @include('shopboss::purchase.partials.actions')
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th class="text-danger text-center" colspan="{{ settings()->enable_branch == 1 ? '10' : '9' }}">{{ __('shopboss::shopboss.noDataFound') }}</th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $purchases->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .table-responsive {
        padding-bottom: 10rem;
        padding-top: 2rem;
    }
</style>
@endpush
@endsection