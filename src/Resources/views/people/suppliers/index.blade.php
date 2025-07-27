@extends('isotope::master')

@section('title', __('shopboss::shopboss.suppliers'))

@push('buttons')
    <button type="button" class="btn btn-sm btn-isotope fw-bold" data-bs-toggle="modal" data-bs-target="#supplierCreateModal">
        {{ __('shopboss::shopboss.addSupplier') }}
    </button>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row mb-3">
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['supplier_name'] ?? '' }}" class="form-control form-control-sm" name="search[supplier_name]" placeholder="{{ __('shopboss::shopboss.enterSupplierName') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['supplier_email'] ?? '' }}" class="form-control form-control-sm" name="search[supplier_email]" placeholder="{{ __('shopboss::shopboss.enterSupplierEmail') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['supplier_phone'] ?? '' }}" class="form-control form-control-sm" name="search[supplier_phone]" placeholder="{{ __('shopboss::shopboss.enterSupplierPhone') }}">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['company_name'] ?? '' }}" class="form-control form-control-sm" name="search[company_name]" placeholder="{{ __('shopboss::shopboss.enterCompanyName') }}">
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
                                    <td>{{ __('shopboss::shopboss.supplierName') }}</td>
                                    <td>{{ __('shopboss::shopboss.supplierEmail') }}</td>
                                    <td>{{ __('shopboss::shopboss.supplierPhone') }}</td>
                                    <td>{{ __('shopboss::shopboss.companyName') }}</td>
                                    @if (settings()->enable_branch == 1)
                                    <td>{{ __('shopboss::shopboss.branch') }}</td>
                                    @endif
                                    <td>{{ __('shopboss::shopboss.actions') }}</td>
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
                                    @if (settings()->enable_branch == 1)
                                    <td>{{ $supplier->branch->name ?? 'N/A' }}</td>
                                    @endif
                                    <td class="d-flex justify-content-center">
                                        <a title="{{ __('shopboss::shopboss.show') }}"
                                            class="btn btn-outline btn-outline-dashed btn-outline-primary p-0 me-1"
                                            href="{{ route('suppliers.show', $supplier->uuid) }}">
                                            <i class="fas fa-eye ms-1"></i>
                                        </a>
                                        <a title="{{ __('shopboss::shopboss.edit') }}"
                                            class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                            href="{{ route('suppliers.edit', $supplier->uuid) }}">
                                            <i class="fas fa-edit ms-1"></i>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $supplier->uuid) }}"
                                            method="post">
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
                                        <th class="font-weight-bold text-center text-danger" colspan="{{ settings()->enable_branch == 1 ? '7' : '6' }}">{{ __('shopboss::shopboss.noDataFound') }}!</th>
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
                    <h5 class="modal-title" id="supplierCreateModalLabel">{{ __('shopboss::shopboss.createSupplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if (settings()->enable_branch == 1)
                            <div class="mb-2">
                                <label class="form-label">{{ __('shopboss::shopboss.branch') }} <span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-select form-select-sm" data-control="select2" data-placeholder="{{ __('shopboss::shopboss.selectBranch') }}">
                                    <option value="" disabled selected>{{ __('shopboss::shopboss.selectBranch') }}</option>
                                    @if (isset(Auth::user()->branch->id))
                                        <option value="{{ Auth::user()->branch->id }}" selected>{{ Auth::user()->branch->name }}</option>
                                    @else
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"> {{ $branch->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                              
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="form-label" for="supplier_name">{{ __('shopboss::shopboss.supplierName') }} <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm" type="text" name="supplier_name" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="supplier_email">{{ __('shopboss::shopboss.supplierEmail') }}</label>
                            <input class="form-control form-control-sm" type="text" name="supplier_email">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="supplier_phone">{{ __('shopboss::shopboss.supplierPhone') }} <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm" type="text" name="supplier_phone" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="company_name">{{ __('shopboss::shopboss.companyName') }}</label>
                            <input class="form-control form-control-sm" type="text" name="company_name">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="city">{{ __('shopboss::shopboss.city') }}</label>
                            <input class="form-control form-control-sm" type="text" name="city">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="country">{{ __('shopboss::shopboss.country') }}</label>
                            <input class="form-control form-control-sm" type="text" name="country">
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="address">{{ __('shopboss::shopboss.address') }}</label>
                            <input class="form-control form-control-sm" type="text" name="address">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary">
                            {{ __('shopboss::shopboss.create') }} 
                            <i class="fa-solid fa-paper-plane ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

