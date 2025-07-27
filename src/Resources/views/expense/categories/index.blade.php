@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.expenseCategories'))

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('shopboss::shopboss.expenses') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.categories') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('pos::utils.alerts')
                <div class="card">
                    <div class="card-body">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryCreateModal">
                            {{ __('shopboss::shopboss.addCategory') }} <i class="bi bi-plus"></i>
                        </button>

                        <hr>

                        <div class="table-responsive">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="categoryCreateModal" tabindex="-1" role="dialog" aria-labelledby="categoryCreateModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryCreateModalLabel">{{ __('shopboss::shopboss.createCategory') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('expense-categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_name">{{ __('shopboss::shopboss.categoryName') }} <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="category_name" required>
                        </div>
                        <div class="form-group">
                            <label for="category_description">{{ __('shopboss::shopboss.description') }}</label>
                            <textarea class="form-control" name="category_description" id="category_description" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('shopboss::shopboss.create') }} <i class="bi bi-check"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    {!! $dataTable->scripts() !!}
@endpush
@endpush
