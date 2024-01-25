@extends('isotope::master')

@section('title', 'Branch Create')

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('branches.index') }}">List</a>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('branches.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label>{{ __('Branch No') }}</label>
                        <input type="text" class="form-control" name="branch_no" required>
                    </div>
                    <div class="mb-2">
                        <label>{{ __('Branch Name') }}</label>
                        <input type="text" class="form-control" name="branch_name" required>
                    </div>
                    <div class="mb-2">
                        <label>{{ __('Branch Location') }}</label>
                        <input type="text" class="form-control" name="branch_location" required>
                    </div>
                    <div class="mb-2">
                        <label>{{ __('Branch Desciption') }}</label>
                        <input type="text" class="form-control" name="branch_description">
                    </div>
                    <div class="mt-3 float-end">
                        <button type="submit" class="btn btn-sm btn-isotope">
                            {{ __('Create Branch') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection