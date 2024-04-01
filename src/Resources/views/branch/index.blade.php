@extends('isotope::master')

@section('title', 'Branch List')

@push('buttons')
<a class="btn btn-sm btn-isotope fw-bold" href="{{ route('shopboss-branches.create') }}">Create</a>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped h-100">
                        <thead class="bg-isotope">
                            <tr>
                                <td>{{ __('Branch No') }}</td>
                                <td>{{ __('Branch Name') }}</td>
                                <td>{{ __('Branch Location') }}</td>
                                <td>{{ __('Branch Desciption') }}</td>
                                <td>{{ __('Actions') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($branches as $branch)
                            <tr class="text-center">
                                <td>{{ $branch->branch_no }}</td>
                                <td>{{ $branch->branch_name }}</td>
                                <td>{{ $branch->branch_location }}</td>
                                <td>{{ $branch->branch_description }}</td>
                                <td class="d-flex justify-content-center">
                                    <a title="Edit" class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                        href="{{ route('shopboss-branches.edit', $branch->id) }}">
                                        <i class="fas fa-edit ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr class="text-center">
                                <td colspan="5"></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection