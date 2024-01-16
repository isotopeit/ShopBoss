<script src="{{ mix('js/app.js') }}"></script>
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script> --}}
<script src="{{ asset('js/scroll.js') }}"></script>
<script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/utilities.js') }}"></script>

@include('sweetalert::alert')

@yield('third_party_scripts')

@livewireScripts

@stack('page_scripts')