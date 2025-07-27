@extends('isotope::master')

@section('title', __('shopboss::shopboss.editProductCategory'))

@push('buttons')
<a href="{{ route('product-categories.index') }}" class="btn btn-sm btn-isotope fw-bold">{{ __('shopboss::shopboss.list') }}</a>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('product-categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('patch')
                    <div class="mb-2">
                        <label class="form-label" for="category_code">{{ __('shopboss::shopboss.categoryCode') }} <span
                                class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="category_code" required
                            value="{{ $category->category_code }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="category_name">{{ __('shopboss::shopboss.categoryName') }} <span
                                class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="category_name" required
                            value="{{ $category->category_name }}">
                    </div>
                    @if (settings()->enable_branch == 1)
                    <div class="mb-2">
                        <label class="form-label">{{ __('shopboss::shopboss.branch') }}</label>
                        @php $userBranch = Auth::user()->branch ?? null; @endphp
                        <select name="branch_id" class="form-select form-select-sm" data-control="select2" 
                            data-placeholder="{{ __('shopboss::shopboss.selectBranch') }}" @if ($userBranch) disabled @endif>
                            <option value="" disabled>{{ __('shopboss::shopboss.selectBranch') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    @if (($userBranch && $userBranch->id == $branch->id) || $category->branch_id == $branch->id) selected @endif>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($userBranch)
                            <input type="hidden" name="branch_id" value="{{ $userBranch->id }}">
                        @endif
                    </div>
                    @endif
                    <div class="my-5 text-center">
                        <button type="submit" class="btn btn-sm bg-isotope text-white">{{ __('shopboss::shopboss.update') }} <i
                                class="fa-solid fa-paper-plane ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection