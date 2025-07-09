@extends('isotope::master')

@section('title', 'Update Supplier')

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('suppliers.index') }}">
        {{ __('Supplier List') }}
    </a>
@endpush

@section('content')
    <div class="container-fluid">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">                
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if (settings()->enable_branch == 1)
                            <div class="form-group mb-3">
                                <label for="branch_id">{{ __('Branch') }} <span class="text-danger">*</span></label>
                                @php $userBranch = Auth::user()->branch ?? null; @endphp
                                <select name="branch_id" class="form-select form-select-sm" data-control="select2" 
                                    data-placeholder="{{ __('Select Branch') }}" @if ($userBranch) disabled @endif>
                                    <option value="" disabled>{{ __('Select Branch') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            @if (($userBranch && $userBranch->id == $branch->id) || $supplier->branch_id == $branch->id) selected @endif>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($userBranch)
                                    <input type="hidden" name="branch_id" value="{{ $userBranch->id }}">
                                @endif
                            </div>
                            @endif
                            
                            <div class="form-row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="supplier_name">{{ __('Supplier Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="supplier_name" required value="{{ $supplier->supplier_name }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="address">{{ __('Company Name') }}</label>
                                        <input type="text" class="form-control" name="company_name" value="{{ $supplier->company_name }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="supplier_phone">{{ __('Phone') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="supplier_phone" required value="{{ $supplier->supplier_phone }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="city">{{ __('City') }}</label>
                                        <input type="text" class="form-control" name="city" value="{{ $supplier->city }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="country">{{ __('Country') }}</label>
                                        <input type="text" class="form-control" name="country" value="{{ $supplier->country }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="address">{{ __('Address') }}</label>
                                        <input type="text" class="form-control" name="address" value="{{ $supplier->address }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="supplier_email">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" name="supplier_email" value="{{ $supplier->supplier_email }}">
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3">
                                    <div class="form-group">
                                        <button class="btn btn-primary float-end">{{ __('Update Supplier') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

