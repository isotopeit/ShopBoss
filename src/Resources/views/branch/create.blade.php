@extends('isotope::master')

@section('title', __("hrm::hrm.createBranch"))

@push('buttons')
<a href="{{ route('therapy-branches.index') }}" class="btn btn-sm btn-isotope fw-bold">{{ __("hrm::hrm.list") }}</a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('therapy-branches.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md 6">
                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __("hrm::hrm.branchName") }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="name" type="text" placeholder="{{ __("hrm::hrm.exBranchName") }}" class="form-control form-control-sm" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __("hrm::hrm.branchAddress") }}:</label>
                        <div class="col-md-9 col-12">
                            <textarea name="address" placeholder="{{ __("hrm::hrm.exBranchAddress") }}" class="form-control form-control-sm" rows="1" value="" required>{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __("hrm::hrm.branchPhoneNumber") }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="phone" type="tel" placeholder="{{ __("hrm::hrm.exBranchPhone") }}" class="form-control form-control-sm" value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __("hrm::hrm.branchHead") }}:</label>
                        <div class="col-md-9 col-12">
                            <select name="branch_head" class="form-select form-select-sm" data-control="select2" data-plceholder="{{ __("hrm::hrm.choose") }}...">
                                <option value="" disabled>{{ __("hrm::hrm.choose") }}..</option>
                                @foreach ($persons as $person)
                                <option value="{{ $person->id }}" {{ old('branch_head') == $person->id ? 'selected' : '' }}>{{$person->fullname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __("hrm::hrm.branchLocationLatitude") }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="branch_location_latitude" type="text" placeholder="{{ __("hrm::hrm.exBranchLatitude") }}" class="form-control form-control-sm" value="{{ old('branch_location_latitude') }}" required>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __("hrm::hrm.branchLocationLongitude") }}:</label>
                        <div class="col-md-9 col-12">
                            <input name="branch_location_longitude" type="text" placeholder="{{ __("hrm::hrm.exBranchLongitude") }}" class="form-control form-control-sm" value="{{ old('branch_location_longitude') }}" required>
                        </div>
                    </div>
                    <div class="mb-1 row">
                        <label class="font-weight-semibold col-form-label col-md-3 col-12">{{ __("hrm::hrm.branchStatus") }}:</label>
                        <div class="col-md-9 col-12">
                            <div class="form-check form-switch mt-4">
                                <input name="status" class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                    </div>

                    <div class="text-end my-3">
                        <button type="submit" title="{{ __("hrm::hrm.saveData") }}" class="btn btn-sm btn-isotope fw-bold">
                            <i class="fa-solid fa-paper-plane fs-4 me-2"></i>
                            {{ __("hrm::hrm.saveBranch") }}
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection
