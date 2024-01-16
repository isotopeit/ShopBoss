@extends('isotope::master')

@section('title', 'Supplier Details')

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('suppliers.index') }}">
        {{ __('Supplier List') }}
    </a>
    &nbsp; &nbsp;

    <button type="button" class="btn btn-sm btn-isotope fw-bold" data-bs-toggle="modal" data-bs-target="#supplierCreateModal">
        {{ __('Add Supplier') }}
    </button>

@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>{{ __('Supplier Name') }}</th>
                    <td>{{ $supplier->supplier_name }}</td>
                </tr>
                <tr>
                    <th>{{ __('Supplier Email') }}</th>
                    <td>{{ $supplier->supplier_email }}</td>
                </tr>
                <tr>
                    <th>{{ __('Supplier Phone') }}</th>
                    <td>{{ $supplier->supplier_phone }}</td>
                </tr>
                <tr>
                    <th>{{ __('Company Name') }}</th>
                    <td>{{ $supplier->company_name }}</td>
                </tr>
                <tr>
                    <th>{{ __('City') }}</th>
                    <td>{{ $supplier->city }}</td>
                </tr>
                <tr>
                    <th>{{ __('Country') }}</th>
                    <td>{{ $supplier->country }}</td>
                </tr>
                <tr>
                    <th>{{ __('Address') }}</th>
                    <td>{{ $supplier->address }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

