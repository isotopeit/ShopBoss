@extends('isotope::master')

@section('title', __('shopboss::shopboss.salesList'))

@push('buttons')
    <a href="{{ route('sales.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
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
                <input type="text" value="{{ Request::input('search')['customer_name'] ?? '' }}"
                    class="form-control form-control-sm" name="search[customer_name]"
                    placeholder="{{ __('shopboss::shopboss.enterCustomerName') }}">
            </div>
            <div class="col-md">
                <input type="date" value="{{ Request::input('search')['date'] ?? '' }}"
                    class="form-control form-control-sm" name="search[date]"
                    placeholder="{{ __('shopboss::shopboss.enterDate') }}">
            </div>
            <div class="col-md">
                <select class="form-select form-select-sm" name="search[status]">
                    <option value="">{{ __('shopboss::shopboss.selectStatus') }}</option>
                    <option value="Pending" {{ (Request::input('search')['status'] ?? '') == 'Pending' ? 'selected' : '' }}>{{ __('shopboss::shopboss.pending') }}</option>
                    <option value="Ordered" {{ (Request::input('search')['status'] ?? '') == 'Ordered' ? 'selected' : '' }}>{{ __('shopboss::shopboss.ordered') }}</option>
                    <option value="Completed" {{ (Request::input('search')['status'] ?? '') == 'Completed' ? 'selected' : '' }}>{{ __('shopboss::shopboss.completed') }}</option>
                </select>
            </div>
           
            <div class="col-md">
                <button type="submit" class="btn btn-sm bg-isotope text-white"><i
                        class="fa-solid fa-search text-white"></i> {{ __('shopboss::shopboss.search') }}</button>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-refresh"></i> {{ __('shopboss::shopboss.reset') }}
                </a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped h-100">
                <thead class="bg-isotope">
                    <tr>
                        <td>{{ __('shopboss::shopboss.slNo') }}</td>
                        <td>{{ __('shopboss::shopboss.reference') }}</td>
                        <td>{{ __('shopboss::shopboss.customer') }}</td>
                        <td>{{ __('shopboss::shopboss.totalAmount') }}</td>
                        <td>{{ __('shopboss::shopboss.paidAmount') }}</td>
                        <td>{{ __('shopboss::shopboss.dueAmount') }}</td>
                        <td>{{ __('shopboss::shopboss.paymentStatus') }}</td>
                        @if (settings()->enable_branch == 1)
                        <td>{{ __('shopboss::shopboss.branch') }}</td>
                        @endif
                        <td>{{ __('shopboss::shopboss.date') }}</td>
                        <td>{{ __('shopboss::shopboss.status') }}</td>
                        <td>{{ __('shopboss::shopboss.actions') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->reference }}</td>
                        <td>{{ $sale->customer_name }}</td>
                        <td>{{ $sale->total_amount }}</td>
                        <td>{{ $sale->paid_amount }}</td>
                        <td>{{ $sale->due_amount }}</td>
                        <td>
                            <span class="badge badge-{{ $sale->payment_status == 'Paid' ? 'success' : ($sale->payment_status == 'Partial' ? 'warning' : 'danger') }}">
                                {{ $sale->payment_status }}
                            </span>
                        </td>
                        @if (settings()->enable_branch == 1)
                        <td>{{ $sale->branch->name ?? 'N/A' }}</td>
                        @endif
                        <td>{{ $sale->date }}</td>
                        <td>
                            <span class="badge badge-{{ $sale->status == 'Completed' ? 'success' : ($sale->status == 'Ordered' ? 'info' : 'warning') }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                        <td class="d-flex justify-content-center">
                            @include('shopboss::sale.partials.actions')
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <th class="text-center text-danger" colspan="{{ settings()->enable_branch == 1 ? '11' : '10' }}">{{ __('shopboss::shopboss.noDataFound') }}</th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sales->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@push('css')
<style>
    .table-responsive {
        padding-bottom: 10rem;
        padding-top: 2rem;
    }
    .badge {
        font-size: 0.75em;
        padding: 0.25em 0.5em;
    }
</style>
@endpush
@endsection
