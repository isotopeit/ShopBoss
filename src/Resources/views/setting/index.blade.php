@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.editSettings'))

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.settings') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('pos::utils.alerts')
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('shopboss::shopboss.generalSettings') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <h5 class="text-center font-weight-bold">{{ __('shopboss::shopboss.companySetting') }}</h5>
                            <hr>
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_name">{{ __('shopboss::shopboss.companyName') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_name"
                                            value="{{ $settings->company_name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_email">{{ __('shopboss::shopboss.companyEmail') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_email"
                                            value="{{ $settings->company_email }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_phone">{{ __('shopboss::shopboss.companyPhone') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_phone"
                                            value="{{ $settings->company_phone }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="default_currency_id">{{ __('shopboss::shopboss.defaultCurrency') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="default_currency_id" id="default_currency_id" class="form-control"
                                            required>
                                            @foreach ($currencies as $currency)
                                                <option
                                                    {{ $settings->default_currency_id == $currency->id ? 'selected' : '' }}
                                                    value="{{ $currency->id }}">{{ $currency->currency_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="default_currency_position">{{ __('shopboss::shopboss.defaultCurrencyPosition') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="default_currency_position" id="default_currency_position"
                                            class="form-control" required>
                                            <option
                                                {{ $settings->default_currency_position == 'prefix' ? 'selected' : '' }}
                                                value="prefix">{{ __('shopboss::shopboss.prefix') }}</option>
                                            <option
                                                {{ $settings->default_currency_position == 'suffix' ? 'selected' : '' }}
                                                value="suffix">{{ __('shopboss::shopboss.suffix') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="notification_email">{{ __('shopboss::shopboss.notificationEmail') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="notification_email"
                                            value="{{ $settings->notification_email }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="company_address">{{ __('shopboss::shopboss.companyAddress') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_address"
                                            value="{{ $settings->company_address }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="footer_text">{{ __('shopboss::shopboss.footerText') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea rows="1" name="footer_text" class="form-control">{!! $settings->footer_text !!}</textarea>
                                    </div>
                                </div>
                            </div>

                            @if (auth()->user()->role_id == 1)
                                <hr>
                                <h5 class="text-center font-weight-bold">{{ __('shopboss::shopboss.brandingSetting') }}</h5>
                                <hr>
                                <div class="form-row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.logoBgColor') }}</label>
                                            <input type="text" class="form-control" data-coloris name="logo_bg_color"
                                                value="{{ $settings->logo_bg_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.sidebarBgColor') }}</label>
                                            <input type="text" class="form-control" data-coloris name="sidebar_bg_color"
                                                value="{{ $settings->sidebar_bg_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.menuActiveColor') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="menu_active_color"
                                                value="{{ $settings->menu_active_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.menuHoverColor') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="menu_hover_color"
                                                value="{{ $settings->menu_hover_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.buttonColor') }}</label>
                                            <input type="text" class="form-control" data-coloris name="button_color"
                                                value="{{ $settings->button_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.cardHeaderColor') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="card_header_color"
                                                value="{{ $settings->card_header_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.tableHeaderColor') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="table_header_color" id="table_header_color"
                                                value="{{ $settings->table_header_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('shopboss::shopboss.logo') }}</label>
                                            <input type="file" class="form-control" name="logo">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary float-right"><i class="bi bi-check"></i>
                                    {{ __('shopboss::shopboss.saveChanges') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('shopboss::shopboss.excelImport') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <input class="form-control" type="file" id="product_input" accept=".xls,.xlsx" />
                            </div>
                            <div class="col-1">
                                <button class="btn btn-dark" id="product_load">{{ __('shopboss::shopboss.load') }}</button>
                            </div>
                            <div class="col-1">
                                <button class="btn btn-success" id="product_upload">{{ __('shopboss::shopboss.upload') }}</button>
                            </div>
                        </div>

                        <div class="table-responsive mt-3" id="table-area">
                            <table id="xl-table" class="table table-bordered d-none">
                                <thead class="bg-dark text-white"></thead>
                                <tbody style="background: #fff"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                @if (session()->has('settings_smtp_message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            <span>{{ session('settings_smtp_message') }}</span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('shopboss::shopboss.mailSettings') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.smtp.update') }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_mailer">{{ __('shopboss::shopboss.mailMailer') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_mailer"
                                            value="{{ env('MAIL_MAILER') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_host">{{ __('shopboss::shopboss.mailHost') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_host"
                                            value="{{ env('MAIL_HOST') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_port">{{ __('shopboss::shopboss.mailPort') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="mail_port"
                                            value="{{ env('MAIL_PORT') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_mailer">{{ __('shopboss::shopboss.mailMailer') }}</label>
                                        <input type="text" class="form-control" name="mail_mailer"
                                            value="{{ env('MAIL_MAILER') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_username">{{ __('shopboss::shopboss.mailUsername') }}</label>
                                        <input type="text" class="form-control" name="mail_username"
                                            value="{{ env('MAIL_USERNAME') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_password">{{ __('shopboss::shopboss.mailPassword') }}</label>
                                        <input type="password" class="form-control" name="mail_password"
                                            value="{{ env('MAIL_PASSWORD') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="mail_encryption">{{ __('shopboss::shopboss.mailEncryption') }}</label>
                                        <input type="text" class="form-control" name="mail_encryption"
                                            value="{{ env('MAIL_ENCRYPTION') }}">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="mail_from_address">{{ __('shopboss::shopboss.mailFromAddress') }}</label>
                                        <input type="email" class="form-control" name="mail_from_address"
                                            value="{{ env('MAIL_FROM_ADDRESS') }}">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="mail_from_name">{{ __('shopboss::shopboss.mailFromName') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_from_name"
                                            value="{{ env('MAIL_FROM_NAME') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary float-right"><i class="bi bi-check"></i>
                                    {{ __('shopboss::shopboss.saveChanges') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_css')
    <link rel="stylesheet" href="{{ asset('/css/coloris.min.css') }}">
@endpush
@push('page_scripts')
    <script src="{{ asset('/js/coloris.min.js') }}"></script>
    <script src="{{ asset('/js/lodash.min.js') }}"></script>
    <script src="{{ asset('/js/moment.js') }}"></script>
    <script src="{{ asset('/js/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('/js/product-xl-import.js?cache='.uniqid()) }}"></script>
@endpush
