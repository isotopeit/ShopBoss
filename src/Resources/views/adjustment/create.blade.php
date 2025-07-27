@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.createAdjustment'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">{{ __('shopboss::shopboss.adjustments') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.add') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('pos::utils.alerts')
                        <form action="{{ route('adjustments.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="reference">{{ __('shopboss::shopboss.reference') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="ADJ">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="date">{{ __('shopboss::shopboss.date') }} <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date" required value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <livewire:adjustment.product-table/>
                            <div class="form-group">
                                <label for="note">{{ __('shopboss::shopboss.noteIfNeeded') }}</label>
                                <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mt-3 float-right">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('shopboss::shopboss.createAdjustment') }} <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
