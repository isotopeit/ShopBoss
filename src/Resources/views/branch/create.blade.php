@extends('isotope::master')

@section('title', 'Branch Edit')

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
                    <div class="form-group">
                        <label>{{ __('Branch No') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="branch_no" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Branch Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="branch_name" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Branch Location') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="branch_location" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Branch Desciption') }} <span class="text-danger">*</span></label>
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