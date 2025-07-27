@extends('isotope::master')

@section('title', __('shopboss::shopboss.branchView'))

@push('buttons')
<a href="{{ route('therapy-branches.index') }}" class="btn btn-sm btn-isotope fw-bold">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover table-striped">
                    <tr>
                        <th>{{ __('shopboss::shopboss.branchName') }}</th>
                        <td>{{ $branch->name??'N/A' }}</td>
                        <th>{{ __('shopboss::shopboss.branchAddress') }}</th>
                        <td>{{ $branch->address??'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('shopboss::shopboss.branchHead') }}</th>
                        <td>{{ $branch->branchHead->fullname??'N/A' }}</td>
                        <th>{{ __('shopboss::shopboss.branchPhoneNumber') }}</th>
                        <td>{{ $branch->phone??'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('shopboss::shopboss.branchLocationLatitude') }}</th>
                        <td>{{ number_format($branch->branch_location_latitude ?? '0.00', 2) }}</td>
                        <th>{{ __('shopboss::shopboss.branchLocationLongitude') }}</th>
                        <td>{{ number_format($branch->branch_location_longitude ?? '0.00', 2) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('shopboss::shopboss.status') }}</th>
                        <td>{{ $branch->is_active ? __('shopboss::shopboss.active') : __('shopboss::shopboss.deactive') }}</td>
                        <th></th>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
