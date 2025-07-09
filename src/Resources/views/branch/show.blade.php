@extends('isotope::master')

@section('title', __("hrm::hrm.branchView"))

@push('buttons')
<a href="{{ route('therapy-branches.index') }}" class="btn btn-sm btn-isotope fw-bold">{{ __("hrm::hrm.list") }}</a>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped">
                    <tr>
                        <th>{{ __("hrm::hrm.branchName") }}</th>
                        <td>{{ $branch->name??'N/A' }}</td>
                        <th>{{ __("hrm::hrm.branchAddress") }}</th>
                        <td>{{ $branch->address??'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __("hrm::hrm.branchHead") }}</th>
                        <td>{{ $branch->branchHead->fullname??'N/A' }}</td>
                        <th>{{ __("hrm::hrm.branchPhoneNumber") }}</th>
                        <td>{{ $branch->phone??'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __("hrm::hrm.branchLocationLatitude") }}</th>
                        <td>{{ number_format($branch->branch_location_latitude ?? '0.00', 2) }}</td>
                        <th>{{ __("hrm::hrm.branchLocationLongitude") }}</th>
                        <td>{{ number_format($branch->branch_location_longitude ?? '0.00', 2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __("hrm::hrm.status") }}</th>
                        <td>{{ $branch->is_active ? __("hrm::hrm.active") : __("hrm::hrm.deactive") }}</td>
                        <th></th>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
