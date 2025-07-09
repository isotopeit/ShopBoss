@extends('isotope::master')

@section('title', 'Sale Returns List')

@push('buttons')
    <a href="{{ route('sale-returns.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('Create') }}
    </a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form class="row mb-3">
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['reference'] ?? '' }}"
                    class="form-control form-control-sm" name="search[reference]"
                    placeholder="{{ __('Enter Reference') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['customer_name'] ?? '' }}"
                    class="form-control form-control-sm" name="search[customer_name]"
                    placeholder="{{ __('Enter Customer Name') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['date'] ?? '' }}"
                    class="form-control form-control-sm" name="search[date]"
                    placeholder="{{ __('Enter Date') }}">
            </div>
            <div class="col-md">
                <input type="text" value="{{ Request::input('search')['payment_status'] ?? '' }}"
                    class="form-control form-control-sm" name="search[payment_status]"
                    placeholder="{{ __('Enter Payment Status') }}">
            </div>
            @if (settings()->enable_branch == 1)
            <div class="col-md">
                <select name="search[branch_id]" class="form-select form-select-sm" data-control="select2" data-placeholder="Select Branch">
                    <option value="">All Branches</option>
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
        <div class="table-responsive"  style="min-height: 40vh">
            <table class="table table-sm table-bordered table-striped h-100">
                <thead class="bg-isotope">
                    <tr>
                        <td>{{ __('#SL') }}</td>
                        <td>{{ __('Reference') }}</td>
                        <td>{{ __('Customer') }}</td>
                        <td>{{ __('Total Amount') }}</td>
                        <td>{{ __('Paid Amount') }}</td>
                        <td>{{ __('Due Amount') }}</td>
                        <td>{{ __('Payment Status') }}</td>
                        @if (settings()->enable_branch == 1)
                        <td>{{ __('Branch') }}</td>
                        @endif
                        <td>{{ __('Date') }}</td>
                        <td>{{ __('Actions') }}</td>
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
                            <th class="text-center text-danger" colspan="{{ settings()->enable_branch == 1 ? '11' : '10' }}">{{ __('No Data Found!') }}</th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sale_returns->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
