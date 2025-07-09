@extends('isotope::master')

@section('title', __("hrm::hrm.branchUserList"))

@push('buttons')
    <a href="{{ route('shop-branch-user.index') }}" class="btn btn-sm btn-isotope fw-bold">{{ __("hrm::hrm.index") }}</a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('shop-branch-user.store') }}" method="POST">
            @csrf
            @include('therapy::branch_user.form')

            <div class="text-end my-3">
                <button type="submit" title="{{ __("hrm::hrm.saveData") }}" class="btn btn-sm btn-isotope fw-bold">
                    <i class="fa-solid fa-paper-plane fs-4 me-2"></i>
                    {{ __("hrm::hrm.save") }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
