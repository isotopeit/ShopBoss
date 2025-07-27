@extends('isotope::master')

@section('title', __('shopboss::shopboss.supplierDetails'))

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('suppliers.index') }}">
        {{ __('shopboss::shopboss.supplierList') }}
    </a>
    &nbsp; &nbsp;

    <button type="button" class="btn btn-sm btn-isotope fw-bold" data-bs-toggle="modal" data-bs-target="#supplierCreateModal">
        {{ __('shopboss::shopboss.addSupplier') }}
    </button>

@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>{{ __('shopboss::shopboss.supplierName') }}</th>
                    <td>{{ $supplier->supplier_name }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.supplierEmail') }}</th>
                    <td>{{ $supplier->supplier_email }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.supplierPhone') }}</th>
                    <td>{{ $supplier->supplier_phone }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.companyName') }}</th>
                    <td>{{ $supplier->company_name }}</td>
                </tr>
                @if (settings()->enable_branch == 1)
                <tr>
                    <th>{{ __('shopboss::shopboss.branch') }}</th>
                    <td>{{ $supplier->branch->name ?? 'N/A' }}</td>
                </tr>
                @endif
                <tr>
                    <th>{{ __('shopboss::shopboss.city') }}</th>
                    <td>{{ $supplier->city }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.country') }}</th>
                    <td>{{ $supplier->country }}</td>
                </tr>
                <tr>
                    <th>{{ __('shopboss::shopboss.address') }}</th>
                    <td>{{ $supplier->address }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

