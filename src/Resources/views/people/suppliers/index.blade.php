@extends('isotope::master')

@section('title', 'Suppliers')

@push('buttons')
    <button type="button" class="btn btn-sm btn-isotope fw-bold" data-bs-toggle="modal" data-bs-target="#supplierCreateModal">
        {{ __('Add Supplier') }}
    </button>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row mb-3">
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['supplier_name'] ?? '' }}" class="form-control form-control-sm" name="search[supplier_name]" placeholder="{{ __('Enter Supplier Name') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['supplier_email'] ?? '' }}" class="form-control form-control-sm" name="search[supplier_email]" placeholder="{{ __('Enter Supplier Email') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['supplier_phone'] ?? '' }}" class="form-control form-control-sm" name="search[supplier_phone]" placeholder="{{ __('Enter Supplier Phone') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['company_name'] ?? '' }}" class="form-control form-control-sm" name="search[company_name]" placeholder="{{ __('Enter Company Name') }}">
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
                                    <td>{{ __('Supplier Name') }}</td>
                                    <td>{{ __('Supplier Email') }}</td>
                                    <td>{{ __('Supplier Phone') }}</td>
                                    <td>{{ __('Company Name') }}</td>
                                    <td>{{ __('Actions') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $supplier->supplier_name }}</td>
                                    <td>{{ $supplier->supplier_email }}</td>
                                    <td>{{ $supplier->supplier_phone }}</td>
                                    <td>{{ $supplier->company_name }}</td>
                                    <td class="d-flex justify-content-center">
                                        <a title="Show"
                                            class="btn btn-outline btn-outline-dashed btn-outline-primary p-0 me-1"
                                            href="{{ route('suppliers.show', $supplier->uuid) }}">
                                            <i class="fas fa-eye ms-1"></i>
                                        </a>
                                        <a title="Edit"
                                            class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                            href="{{ route('suppliers.edit', $supplier->uuid) }}">
                                            <i class="fas fa-edit ms-1"></i>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $supplier->uuid) }}"
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
                                        <th class="font-weight-bold text-center text-danger" colspan="6">No Data Found!</th>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $suppliers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="supplierCreateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierCreateModalLabel">{{ __('Create Supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label" for="supplier_name">{{ __('Supplier Name') }} <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm" type="text" name="supplier_name" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="supplier_email">{{ __('Supplier Email') }}</label>
                            <input class="form-control form-control-sm" type="text" name="supplier_email">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="supplier_phone">{{ __('Supplier Phone') }} <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm" type="text" name="supplier_phone" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="company_name">{{ __('Company Name') }}</label>
                            <input class="form-control form-control-sm" type="text" name="company_name">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="city">{{ __('City') }}</label>
                            <input class="form-control form-control-sm" type="text" name="city">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="country">{{ __('Country') }}</label>
                            <input class="form-control form-control-sm" type="text" name="country">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="address">{{ __('Address') }}</label>
                            <input class="form-control form-control-sm" type="text" name="address">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary">
                            {{ __('Create') }} 
                            <i class="fa-solid fa-paper-plane ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

