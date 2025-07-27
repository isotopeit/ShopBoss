@extends('isotope::master')

@section('title', __('shopboss::shopboss.editCustomer'))

@push('buttons')
    <a href="{{ route('customers.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('shopboss::shopboss.customerList') }}
    </a>
@endpush

@section('content')
<form action="{{ route('customers.update', $customer) }}" method="POST">
    @csrf
    @method('patch')
    <div class="card">
        <div class="card-body">
            @if (settings()->enable_branch == 1)
            <div class="form-group mb-3">
                <label for="branch_id">{{ __('shopboss::shopboss.branch') }} <span class="text-danger">*</span></label>
                @php $userBranch = Auth::user()->branch ?? null; @endphp
                <select name="branch_id" class="form-select form-select-sm" data-control="select2" 
                    data-placeholder="{{ __('shopboss::shopboss.selectBranch') }}" @if ($userBranch) disabled @endif>
                    <option value="" disabled>{{ __('shopboss::shopboss.selectBranch') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}"
                            @if (($userBranch && $userBranch->id == $branch->id) || $customer->branch_id == $branch->id) selected @endif>
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
                        <label for="customer_name">{{ __('shopboss::shopboss.customerName') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" required value="{{ $customer->customer_name }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="customer_email">{{ __('shopboss::shopboss.email') }} <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="customer_email" required value="{{ $customer->customer_email }}">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="customer_phone">{{ __('shopboss::shopboss.phone') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_phone" required value="{{ $customer->customer_phone }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="city">{{ __('shopboss::shopboss.city') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="city" required value="{{ $customer->city }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="country">{{ __('shopboss::shopboss.country') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="country" required value="{{ $customer->country }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="address">{{ __('shopboss::shopboss.address') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="address" required value="{{ $customer->address }}">
                    </div>
                </div>
                <div class="col-lg-12 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary float-end">{{ __('shopboss::shopboss.updateCustomer') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</form>
@endsection

