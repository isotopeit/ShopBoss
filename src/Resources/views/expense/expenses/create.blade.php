@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.createExpense'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">{{ __('shopboss::shopboss.expenses') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.add') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form id="expense-form" action="{{ route('expenses.store') }}" method="POST">
            @csrf
            <div class="row">
                
                <div class="col-lg-12">
                @include('pos::utils.alerts')

                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">{{ __('shopboss::shopboss.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="EXP">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">{{ __('shopboss::shopboss.date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="category_id">{{ __('shopboss::shopboss.category') }} <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            <option value="" selected>{{ __('shopboss::shopboss.selectCategory') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="amount">{{ __('shopboss::shopboss.amount') }} <span class="text-danger">*</span></label>
                                        <input id="amount" type="text" class="form-control" name="amount" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="details">{{ __('shopboss::shopboss.details') }}</label>
                                <textarea class="form-control" rows="6" name="details"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <button class="btn btn-primary">{{ __('shopboss::shopboss.createExpense') }} <i class="bi bi-check"></i></button>
                </div>
            </div>
        </form>
    </div>
@endsection
@endsection
