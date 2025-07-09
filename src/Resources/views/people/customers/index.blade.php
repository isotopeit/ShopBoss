@extends('isotope::master')

@section('title', 'Customers')

@push('buttons')
    <a href="{{ route('customers.create') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('Add customer') }}
    </a>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row mb-3">
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['customer_name'] ?? '' }}" class="form-control form-control-sm" name="search[customer_name]" placeholder="{{ __('Enter Customer Name') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['customer_email'] ?? '' }}" class="form-control form-control-sm" name="search[customer_email]" placeholder="{{ __('Enter Customer Email') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['customer_phone'] ?? '' }}" class="form-control form-control-sm" name="search[customer_phone]" placeholder="{{ __('Enter Customer Phone') }}">
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
                            <button type="submit" class="btn btn-sm bg-isotope text-white"><i class="fa-solid fa-search text-white"></i> Search</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped">
                            <thead class="bg-isotope">
                                <tr>
                                    <td>{{ __('#SL') }}</td>
                                    <td>{{ __('Customer Name') }}</td>
                                    <td>{{ __('Customer Email') }}</td>
                                    <td>{{ __('Customer Phone') }}</td>
                                    @if (settings()->enable_branch == 1)
                                    <td>{{ __('Branch') }}</td>
                                    @endif
                                    <td>{{ __('Actions') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->customer_name }}</td>
                                    <td>{{ $customer->customer_email }}</td>
                                    <td>{{ $customer->customer_phone }}</td>
                                    @if (settings()->enable_branch == 1)
                                    <td>{{ $customer->branch->name ?? 'N/A' }}</td>
                                    @endif
                                    <td class="d-flex justify-content-center">
                                        <a title="Show"
                                            class="btn btn-outline btn-outline-dashed btn-outline-primary p-0 me-1"
                                            href="{{ route('customers.show', $customer->uuid) }}">
                                            <i class="fas fa-eye ms-1"></i>
                                        </a>
                                        <a title="Edit"
                                            class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                            href="{{ route('customers.edit', $customer->uuid) }}">
                                            <i class="fas fa-edit ms-1"></i>
                                        </a>
                                        <form action="{{ route('customers.destroy', $customer->uuid) }}"
                                            method="post">
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
                                        <th class="font-weight-bold text-center text-danger" colspan="{{ settings()->enable_branch == 1 ? '6' : '5' }}">No Data Found!</th>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

