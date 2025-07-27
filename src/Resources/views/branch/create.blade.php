@extends('isotope::master')

@section('title', __('shopboss::shopboss.createBranch'))

@push('buttons')
<a href="{{ route('therapy-branches.index') }}" class="btn btn-sm btn-isotope fw-bold">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('therapy-branches.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md 6">
                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __('shopboss::shopboss.branchName') }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="name" type="text" placeholder="{{ __('shopboss::shopboss.exBranchName') }}" class="form-control form-control-sm" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __('shopboss::shopboss.branchAddress') }}:</label>
                        <div class="col-md-9 col-12">
                            <textarea name="address" placeholder="{{ __('shopboss::shopboss.exBranchAddress') }}" class="form-control form-control-sm" rows="1" value="" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __('shopboss::shopboss.branchPhoneNumber') }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="phone" type="tel" placeholder="{{ __('shopboss::shopboss.exBranchPhone') }}" class="form-control form-control-sm" value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __('shopboss::shopboss.branchHead') }}:</label>
                        <div class="col-md-9 col-12">
                            <select name="branch_head" class="form-select form-select-sm" data-control="select2" data-plceholder="{{ __('shopboss::shopboss.choose') }}...">
                                <option value="" disabled>{{ __('shopboss::shopboss.choose') }}..</option>
                                @foreach ($persons as $person)
                                <option value="{{ $person->id }}" {{ old('branch_head') == $person->id ? 'selected' : '' }}>{{$person->fullname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __('shopboss::shopboss.branchLocationLatitude') }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="branch_location_latitude" type="text" placeholder="{{ __('shopboss::shopboss.exBranchLatitude') }}" class="form-control form-control-sm" value="{{ old('branch_location_latitude') }}" required>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __('shopboss::shopboss.branchLocationLongitude') }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="branch_location_longitude" type="text" placeholder="{{ __('shopboss::shopboss.exBranchLongitude') }}" class="form-control form-control-sm" value="{{ old('branch_location_longitude') }}" required>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __('shopboss::shopboss.branchStatus') }}:</label>
                        <div class="col-md-9 col-12">
                            <div class="form-check form-switch mt-4">
                                <input name="status" class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                    </div>

                    <div class="text-end my-3">
                        <button type="submit" title="{{ __('shopboss::shopboss.saveData') }}" class="btn btn-sm btn-isotope fw-bold">
                            <i class="fa-solid fa-paper-plane fs-4 me-2"></i>
                            {{ __('shopboss::shopboss.saveBranch') }}
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection
