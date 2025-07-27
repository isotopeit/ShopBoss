@extends('pos::layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8" style="margin-top: 2%">
                <div class="card" style="width: 40rem;">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('shopboss::shopboss.verifyEmailAddress') }}</h4>
                        @if (session('resent'))
                            <p class="alert alert-success" role="alert">{{ __('shopboss::shopboss.freshVerificationLink') }}</p>
                        @endif
                        <p class="card-text">{{ __('shopboss::shopboss.beforeProceeding') }}</p>
                        <a href="{{ route('verification.resend') }}">{{ __('shopboss::shopboss.clickHereToRequest') }}</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection