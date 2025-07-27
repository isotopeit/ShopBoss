@extends('isotope::master')

@section('title', __('shopboss::shopboss.purchaseReturnsList'))

@push('buttons')
    <a href="{{ route('purchase-returns.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('shopboss::shopboss.create') }}
    </a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form class="row mb-3">
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
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['date'] ?? '' }}"
                    class="form-control form-control-sm" name="search[date]"
                    placeholder="{{ __('shopboss::shopboss.enterDate') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['payment_status'] ?? '' }}"
                    class="form-control form-control-sm" name="search[payment_status]"
                    placeholder="{{ __('shopboss::shopboss.enterPaymentStatus') }}">
            </div>
           
            <div class="col-md">
                <button type="submit" class="btn btn-sm bg-isotope text-white"><i
                        class="fa-solid fa-search text-white"></i> {{ __('shopboss::shopboss.search') }}</button>
            </div>
        </form>
        <div class="table-responsive" style="min-height: 40vh">
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
                        <td>{{ __('shopboss::shopboss.date') }}</td>
                        <td>{{ __('shopboss::shopboss.actions') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchase_returns as $purchase_return)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $purchase_return->reference }}</td>
                        <td>{{ $purchase_return->supplier_name }}</td>
                        <td>{{ $purchase_return->total_amount }}</td>
                        <td>{{ $purchase_return->paid_amount }}</td>
                        <td>{{ $purchase_return->due_amount }}</td>
                        <td>{{ $purchase_return->payment_status }}</td>
                        @if (settings()->enable_branch == 1)
                        <td>{{ $purchase_return->branch->name ?? 'N/A' }}</td>
                        @endif
                        <td>{{ $purchase_return->date }}</td>
                        <td class="d-flex justify-content-center">
                            @include('shopboss::purchases-return.partials.actions')
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <th class="text-center text-danger" colspan="{{ settings()->enable_branch == 1 ? '11' : '10' }}">{{ __('shopboss::shopboss.noDataFound!') }}</th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $purchase_returns->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .table-responsive {
        padding-bottom: 10rem;
        padding-top: 2rem;
    }
</style>
@endpush