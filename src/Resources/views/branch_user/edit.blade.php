@extends('isotope::master')

@section('title', __('shopboss::shopboss.branchUserList'))

@push('buttons')
    <a href="{{ route('shop-branch-user.index') }}" class="btn btn-sm btn-isotope fw-bold">{{ __('shopboss::shopboss.index') }}</a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('shop-branch-user.update', $branchUser) }}" method="POST">
            @csrf
            @method('PUT')
            @include('therapy::branch_user.form')

            <div class="text-end my-3">
                <button type="submit" title="{{ __('shopboss::shopboss.saveData') }}" class="btn btn-sm btn-isotope fw-bold">
                    <i class="fa-solid fa-paper-plane fs-4 me-2"></i>
                    {{ __('shopboss::shopboss.update') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
