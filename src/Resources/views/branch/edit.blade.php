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
                <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>{{ __('Branch No') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="branch_no" value="{{ $branch->branch_no }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Branch Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="branch_name" value="{{ $branch->branch_name }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Branch Location') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="branch_location" value="{{ $branch->branch_location }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Branch Desciption') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="branch_description" value="{{ $branch->branch_description }}">
                    </div>
                    <div class="mt-3 float-end">
                        <button type="submit" class="btn btn-sm btn-isotope">
                            {{ __('Update Branch') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection