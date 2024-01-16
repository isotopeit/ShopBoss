@extends('pos::layouts.app')

@section('title', 'Edit Settings')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Settings') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('pos::utils.alerts')
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('General Settings') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <h5 class="text-center font-weight-bold">{{ __('Company Setting') }}</h5>
                            <hr>
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_name">{{ __('Company Name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_name"
                                            value="{{ $settings->company_name }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_email">{{ __('Company Email') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_email"
                                            value="{{ $settings->company_email }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="company_phone">{{ __('Company Phone') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_phone"
                                            value="{{ $settings->company_phone }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="default_currency_id">{{ __('Default Currency') }} <span
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
                                        <label for="default_currency_position">{{ __('Default Currency Position') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="default_currency_position" id="default_currency_position"
                                            class="form-control" required>
                                            <option
                                                {{ $settings->default_currency_position == 'prefix' ? 'selected' : '' }}
                                                value="prefix">Prefix</option>
                                            <option
                                                {{ $settings->default_currency_position == 'suffix' ? 'selected' : '' }}
                                                value="suffix">Suffix</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="notification_email">{{ __('Notification Email') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="notification_email"
                                            value="{{ $settings->notification_email }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="company_address">{{ __('Company Address') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="company_address"
                                            value="{{ $settings->company_address }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="footer_text">{{ __('Footer Text') }} <span
                                                class="text-danger">*</span></label>
                                        <textarea rows="1" name="footer_text" class="form-control">{!! $settings->footer_text !!}</textarea>
                                    </div>
                                </div>
                            </div>

                            @if (auth()->user()->role_id == 1)
                                <hr>
                                <h5 class="text-center font-weight-bold">{{ __('Branding Setting') }}</h5>
                                <hr>
                                <div class="form-row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Logo Bg Color') }}</label>
                                            <input type="text" class="form-control" data-coloris name="logo_bg_color"
                                                value="{{ $settings->logo_bg_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Sidebar Bg Color') }}</label>
                                            <input type="text" class="form-control" data-coloris name="sidebar_bg_color"
                                                value="{{ $settings->sidebar_bg_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Menu Active Color') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="menu_active_color"
                                                value="{{ $settings->menu_active_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Menu Hover Color') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="menu_hover_color"
                                                value="{{ $settings->menu_hover_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Button Color') }}</label>
                                            <input type="text" class="form-control" data-coloris name="button_color"
                                                value="{{ $settings->button_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Card Header Color') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="card_header_color"
                                                value="{{ $settings->card_header_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Table Header Color') }}</label>
                                            <input type="text" class="form-control" data-coloris
                                                name="table_header_color" id="table_header_color"
                                                value="{{ $settings->table_header_color ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="company_address">{{ __('Logo') }}</label>
                                            <input type="file" class="form-control" name="logo">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary float-right"><i class="bi bi-check"></i>
                                    {{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ __('Excel Import') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <input class="form-control" type="file" id="product_input" accept=".xls,.xlsx" />
                            </div>
                            <div class="col-1">
                                <button class="btn btn-dark" id="product_load">Load</button>
                            </div>
                            <div class="col-1">
                                <button class="btn btn-success" id="product_upload">Upload</button>
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
                        <h4 class="mb-0">{{ __('Mail Settings') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.smtp.update') }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_mailer">{{ __('MAIL_MAILER') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_mailer"
                                            value="{{ env('MAIL_MAILER') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_host">{{ __('MAIL_HOST') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_host"
                                            value="{{ env('MAIL_HOST') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_port">{{ __('MAIL_PORT') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="mail_port"
                                            value="{{ env('MAIL_PORT') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_mailer">{{ __('MAIL_MAILER') }}</label>
                                        <input type="text" class="form-control" name="mail_mailer"
                                            value="{{ env('MAIL_MAILER') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_username">{{ __('MAIL_USERNAME') }}</label>
                                        <input type="text" class="form-control" name="mail_username"
                                            value="{{ env('MAIL_USERNAME') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="mail_password">{{ __('MAIL_PASSWORD') }}</label>
                                        <input type="password" class="form-control" name="mail_password"
                                            value="{{ env('MAIL_PASSWORD') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="mail_encryption">{{ __('MAIL_ENCRYPTION') }}</label>
                                        <input type="text" class="form-control" name="mail_encryption"
                                            value="{{ env('MAIL_ENCRYPTION') }}">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="mail_from_address">{{ __('MAIL_FROM_ADDRESS') }}</label>
                                        <input type="email" class="form-control" name="mail_from_address"
                                            value="{{ env('MAIL_FROM_ADDRESS') }}">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="mail_from_name">{{ __('MAIL_FROM_NAME') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="mail_from_name"
                                            value="{{ env('MAIL_FROM_NAME') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary float-right"><i class="bi bi-check"></i>
                                    Save Changes</button>
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
