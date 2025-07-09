@extends('isotope::master')

@section('title', __("hrm::hrm.branchUserList"))

@push('buttons')
    <a href="{{ route('shop-branch-user.create') }}" class="btn btn-sm btn-isotope fw-bold">{{ __("hrm::hrm.create") }}</a>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" class="form-control form-control-sm" name="search[user]" value="{{ Request::input('search')['user'] ?? '' }}" placeholder="{{ __("hrm::hrm.userName") }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" class="form-control form-control-sm" name="search[branch]" value="{{ Request::input('search')['branch'] ?? '' }}" placeholder="{{ __("hrm::hrm.branchName") }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-sm btn-isotope">{{ __("hrm::hrm.filter") }}</button>
                        <a href="{{ route('shop-branch-user.index') }}" class="btn btn-sm btn-danger rounded">{{ __("hrm::hrm.reset") }}</a>
                    </div>
                </div>
            </form>
            <div class="table-responsive mt-2">
                <table class="table table-sm table-bordered table-hover table-striped">
                    <thead class="bg-isotope text-center">
                        <tr>
                            <th>{{ __("hrm::hrm.sl") }}</th>
                            <th>{{ __("hrm::hrm.userName") }}</th>
                            <th>{{ __("hrm::hrm.branchName") }}</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branchUsers as $item)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->user->name ?? '' }}</td>
                                <td>{{ $item->branch->name ?? '' }}</td>
                                <td class="d-flex justify-content-center">
                                    <a title="{{ __("hrm::hrm.edit") }}" class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                        href="{{ route('shop-branch-user.edit', $item->id) }}">
                                        <i class="fas fa-edit ms-1"></i>
                                    </a>
                                    <form action="{{ route('shop-branch-user.destroy', $item->id) }}" method="POST">
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
                            <td colspan="4" class="text-center text-danger">{{ __("hrm::hrm.noDataFound") }}!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $branchUsers->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
