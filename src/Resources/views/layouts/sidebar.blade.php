<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show {{ request()->routeIs('app.pos.*') ? 'c-sidebar-minimized' : '' }}"
    id="sidebar" style="background: {{ settings()->sidebar_bg_color }}">
    <div class="c-sidebar-brand d-md-down-none" style="background: {{ settings()->logo_bg_color }}">
        <a href="{{ route('home') }}">
            <img class="c-sidebar-brand-full" src="{{ settings()->logo ?? asset('images/logo.png') }}" alt="Site Logo"
                width="50">
            <img class="c-sidebar-brand-minimized" src="{{ settings()->logo ?? asset('images/logo.png') }}"
                alt="Site Logo" width="40">
        </a>
    </div>
    <ul class="c-sidebar-nav">
        @include('pos::layouts.menu')
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 692px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 369px;"></div>
        </div>
    </ul>
    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
        data-class="c-sidebar-minimized"></button>
</div>

@php
    $menu_active_color = settings()->menu_active_color;
    $menu_hover_color  = settings()->menu_hover_color;
@endphp

<style>
    .c-sidebar .c-active.c-sidebar-nav-dropdown-toggle,
    .c-sidebar .c-sidebar-nav-link.c-active {
        background: {{ $menu_active_color }};
        color: #fff
    }

    @media (-ms-high-contrast:none),
    (hover:hover) {
        .c-sidebar .c-sidebar-nav-dropdown-toggle:hover,
        .c-sidebar .c-sidebar-nav-link:hover {
            background: {{ $menu_hover_color }};
            color: #fff
        }
</style>
