@extends('pos::layouts.app')

@section('title', 'Purchases Return Report')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Purchases Return Report') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.purchases-return-report :suppliers="\Isotope\ShopBoss\Models\Supplier::all()"/>
    </div>
@endsection
