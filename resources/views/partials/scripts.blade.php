@stack('script');
<script>
    var hostUrl = `{{ asset('cztemp/assets/')}}`
</script>

<script>
    var baseUrl = `{{ url('/') }}`
    // var baseUrl2 = window.location.origin;
</script>

<script src="{{ asset('cztemp/assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{ asset('cztemp/assets/js/scripts.bundle.js')}}"></script>

<script src="{{ asset('cztemp/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}"></script>
<script src="{{ asset('cztemp/assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{ asset('cztemp/assets/js/custom/widgets.js')}}"></script>

<script src="{{ asset('cztemp/assets/custom/js/utils.js')}}"></script>

@yield('scripts');