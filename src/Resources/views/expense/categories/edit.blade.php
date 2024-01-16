@extends('pos::layouts.app')


@section('title', 'Edit Expense Category')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('Expenses') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">{{ __('Categories') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-7">
                @include('pos::utils.alerts')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('expense-categories.update', $expenseCategory) }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <label for="category_name">{{ __('Category Name') }} <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="category_name" required value="{{ $expenseCategory->category_name }}">
                            </div>
                            <div class="form-group">
                                <label for="category_description">{{ __('Description') }}</label>
                                <textarea class="form-control" name="category_description" id="category_description" rows="5">{{ $expenseCategory->category_description }}</textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('Update') }} <i class="bi bi-check"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

