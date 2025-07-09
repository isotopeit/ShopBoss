@extends('isotope::master')

@section('title', 'Product Categories')

@push('buttons')
<button type="button" class="btn btn-sm btn-isotope fw-bold" data-bs-toggle="modal"
    data-bs-target="#categoryCreateModal">
    {{ __('Add Category') }}
</button>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row mb-3">
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['category_code'] ?? '' }}" class="form-control form-control-sm" name="search[category_code]" placeholder="Enter Category Code">
                        </div>
                        <div class="col-md">
                            <input type="text" value="{{ Request::input('search')['category_name'] ?? '' }}" class="form-control form-control-sm" name="search[category_name]" placeholder="Enter Category Name">
                        </div>
                        @if (settings()->enable_branch == 1)
                        <div class="col-md">
                            <select name="search[branch_id]" class="form-select form-select-sm" data-control="select2" data-placeholder="Select Branch">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (Request::input('search')['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md">
                            <button type="submit" class="btn btn-sm bg-isotope text-white"><i class="fa-solid fa-search text-white"></i> Search</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped">
                            <thead class="bg-isotope">
                                <tr>
                                    <td>#SL</td>
                                    <td>Category Code</td>
                                    <td>Category Name</td>
                                    @if (settings()->enable_branch == 1)
                                    <td>Branch</td>
                                    @endif
                                    <td>Actions</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->category_code }}</td>
                                    <td>{{ $category->category_name }}</td>
                                    @if (settings()->enable_branch == 1)
                                    <td>{{ $category->branch->name ?? 'N/A' }}</td>
                                    @endif
                                    <td class="d-flex justify-content-center">
                                        <a title="Edit"
                                            class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                            href="{{ route('product-categories.edit', $category->id) }}">
                                            <i class="fas fa-edit ms-1"></i>
                                        </a>
                                        <form action="{{ route('product-categories.destroy', $category->id) }}"
                                            method="post">
                                            @method('delete') @csrf
                                            <button title="Delete" type="submit"
                                                class="btn btn-outline btn-outline-dashed btn-outline-danger p-0 me-1">
                                                <i class="fa-solid fa-trash ms-1"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryCreateModalLabel">{{ __('Create Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('product-categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label" for="category_code">{{ __('Category Code') }}</label>
                        <input class="form-control form-control-sm" type="text" name="category_code" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="category_name">{{ __('Category Name') }}</label>
                        <input class="form-control form-control-sm" type="text" name="category_name" required>
                    </div>
                    @if (settings()->enable_branch == 1)
                    <div class="mb-2">
                        <label class="form-label">{{ __('Branch') }}</label>
                        @php $userBranch = Auth::user()->branch ?? null; @endphp
                        <select name="branch_id" class="form-select form-select-sm" data-control="select2" 
                            data-placeholder="{{ __('Select Branch') }}" @if ($userBranch) disabled @endif>
                            <option value="" disabled selected>{{ __('Select Branch') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    @if ($userBranch && $userBranch->id == $branch->id) selected @endif>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($userBranch)
                            <input type="hidden" name="branch_id" value="{{ $userBranch->id }}">
                        @endif
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary">{{ __('Create') }} <i
                            class="fa-solid fa-paper-plane ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection