@extends('isotope::master')

@section('title', __("hrm::hrm.branchList"))

@push('buttons')
@if(settings()->enable_branch == 1)
<a href="{{ route('therapy-branches.create') }}" class="btn btn-sm btn-isotope fw-bold">{{ __("hrm::hrm.create") }}</a>
@endif
@endpush

    @section('content')
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <form action="{{ route('hrmBranchEnable') }}" method="post" id="branchEnableForm" class="mb-0">
                        @csrf
                        <input type="hidden" name="enabled" value="0">
                        <div class="form-check form-switch">
                            <label for="branchSwitch" class="form-check-label">{{ __("hrm::hrm.enableDisableBranch") }}</label>
                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                name="enabled"
                                id="branchSwitch"
                                value="1"
                                {{ settings()->enable_branch ? 'checked' : '' }}
                                onchange="document.getElementById('branchEnableForm').submit();"
                            >
                        </div>
                    </form>
                    @if(settings()->enable_branch == 1)
                        <a class="btn btn-sm btn-isotope rounded" href="{{ route('shop-branch-user.index') }}">
                            {{ __("hrm::hrm.assignUsers") }}
                        </a>
                    @endif
                </div>
            </div>
        </div>


    @php
        use Isotope\HRM\Http\Helpers\DateHelper;
    @endphp
    @if(settings()->enable_branch == 1)
    <div class="card">
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <input type="text" class="form-control form-control-sm" name="search[name]" value="{{ Request::input('search')['name'] ?? '' }}" placeholder="{{ __("hrm::hrm.branchName") }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="text" class="form-control form-control-sm" name="search[phone]" value="{{ Request::input('search')['phone'] ?? '' }}" placeholder="{{ __("hrm::hrm.branchPhoneNumber") }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-sm btn-isotope">{{ __("hrm::hrm.filter") }}</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive mt-2">
                <table class="table table-sm table-bordered table-hover table-striped">
                    <thead class="bg-isotope text-center">
                        <tr>
                            <th>{{ __("hrm::hrm.sl") }}</th>
                            <th>{{ __("hrm::hrm.branchName") }}</th>
                            <th>{{ __("hrm::hrm.branchAddress") }}</th>
                            <th>{{ __("hrm::hrm.branchPhoneNumber") }}</th>
                            <th>{{ __("hrm::hrm.status") }}</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $branch)
                            <tr class="text-center">
                                <td>
                                     {{ app()->getLocale() == 'bn' ? DateHelper::enToBn($loop->iteration) : $loop->iteration }}
                                </td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>{{ $branch->phone }}</td>
                                <td>
                                    @if ($branch->is_active)
                                        <span class="badge bg-success">{{ __("hrm::hrm.yes") }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __("hrm::hrm.no") }}</span>
                                    @endif
                                </td>
                                <td class="d-flex justify-content-center">
                                    <a title="{{ __("hrm::hrm.show") }}" class="btn btn-outline btn-outline-dashed btn-outline-primary p-0 me-1"
                                        href="{{ route('therapy-branches.show',$branch->uuid) }}">
                                        <i class="fas fa-eye ms-1"></i>
                                    </a>
                                    <a title="{{ __("hrm::hrm.edit") }}" class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                        href="{{ route('therapy-branches.edit',$branch->uuid) }}">
                                        <i class="fas fa-edit ms-1"></i>
                                    </a>
                                    <form action="{{ route('therapy-branches.destroy',$branch->uuid) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button title="{{ __("hrm::hrm.delete") }}" onclick="return confirm('{{ __("hrm::hrm.deleteConfirm") }}')" type="submit" class="btn btn-outline btn-outline-dashed btn-outline-danger p-0 me-1">
                                            <i class="fa-solid fa-trash ms-1"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">{{ __("hrm::hrm.noDataFound") }}!</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            {{ $branches->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif

@endsection
