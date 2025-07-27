@extends('isotope::master')

@section('title', __('shopboss::shopboss.saleReturnsList'))

@push('buttons')
    <a href="{{ route('sale-returns.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
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
                <input type="text" value="{{ Request::input('search')['payment_status'] ?? '' }}"
                    class="form-control form-control-sm" name="search[payment_status]"
                    placeholder="{{ __('shopboss::shopboss.enterPaymentStatus') }}">
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
                        class="fa-solid fa-search text-white"></i> {{ __('shopboss::shopboss.search') }}</button>
            </div>
        </form>
        <div class="table-responsive"  style="min-height: 40vh">
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
                        <td>{{ __('shopboss::shopboss.actions') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sale_returns as $sale_return)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale_return->reference }}</td>
                        <td>{{ $sale_return->customer_name }}</td>
                        <td>{{ $sale_return->total_amount }}</td>
                        <td>{{ $sale_return->paid_amount }}</td>
                        <td>{{ $sale_return->due_amount }}</td>
                        <td>{{ $sale_return->payment_status }}</td>
                        @if (settings()->enable_branch == 1)
                        <td>{{ $sale_return->branch->name ?? 'N/A' }}</td>
                        @endif
                        <td>{{ $sale_return->date }}</td>
                        <td class="d-flex justify-content-center">
                            @include('shopboss::salesreturn.partials.actions')
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <th class="text-center text-danger" colspan="{{ settings()->enable_branch == 1 ? '10' : '9' }}">{{ __('shopboss::shopboss.noDataFound') }}</th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sale_returns->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
