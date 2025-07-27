@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.edit'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('shopboss::shopboss.expenses') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="expense-form" action="{{ route('expenses.update', $expense) }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
                
                <div class="col-lg-12">
                @include('pos::utils.alerts')
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">{{ __('shopboss::shopboss.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required value="{{ $expense->reference }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('shopboss::shopboss.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ $expense->getAttributes()['date'] }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="category_id">{{ __('shopboss::shopboss.category') }} <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            @foreach($categories as $category)
                                                <option {{ $category->id == $expense->category_id ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="amount">{{ __('shopboss::shopboss.amount') }} <span class="text-danger">*</span></label>
                                        <input id="amount" type="text" class="form-control" name="amount" required value="{{ $expense->amount }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="details">{{ __('shopboss::shopboss.details') }}</label>
                                <textarea class="form-control" rows="6" name="details">{{ $expense->details }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <button class="btn btn-primary">{{ __('shopboss::shopboss.updateExpense') }} <i class="bi bi-check"></i></button>
                </div>
            </div>
        </form>
    </div>
@endsection
@endsection
