<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- Character Encoding -->
    <meta charset="utf-8">

    <!-- Viewport Settings -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <!-- Metadata -->
    <meta name="organization" content="">
    <meta name="author" content="Doston Olimov">

    <!-- Page Title -->
    <title>KPI tizimi</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('/assets/images/logdo.png') }}">

    <!-- Sidebar Styles -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/simplebar/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/simplebar.css') }}">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- SweetAlert2 Stylesheet -->
{{--    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">--}}

    <!-- DatePicker Stylesheet -->
    <link rel="stylesheet" href="{{ asset('build/css/bootstrap-datetimepicker.min.css') }}">

    <!-- Custom Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/color-style.css') }}">
    <!-- Select2 css files -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    <!-- Table css file -->
    <link rel="stylesheet" href="{{ asset('plugins/tabs/tabs.css') }}">
    <!-- Font awesome min css file -->
    <link rel="stylesheet" href="{{ asset('fonts/fonts/font-awesome.min.css') }}">
    <!-- My style file -->
    <link rel="stylesheet" href="{{ asset('css/myStyle.css') }}">
    <style>
        @font-face {
            font-family: 'DejaVuSans';
            src: url('{{ ('fonts/fonts/dejavu-sans.book.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        .sidebar-nav {
            background-color: #172d50;
        }
        .page-header {
            background-color: #1f6feb !important;
        }
        .sidebar-toggler,
        .sidebar-brand,
        #myBtn {
            background-color: #071d40;
        }
        .header-sticky {
            background-color: #08152c;
        }
    </style>
    <!-- Additional Styles from Other Pages -->
    @yield('styles')
</head>
<body class="app">
<?php
$userid = Auth::user()->id;
// $settings = settings(); // Uncomment if settings are required
?>

    <!-- Sidebar -->
    @include('layouts.blocks.sidebar')

    <!-- Main Wrapper -->
    <div class="wrapper d-flex flex-column min-vh-100 bg-light" style="padding-right: 0!important;">
        <!-- Navbar -->
        @include('layouts.blocks.navbar')

        <!-- Content Area -->
        <div class="body flex-grow-1 px-3">
            @yield('content')
        </div>
        <!-- Content Area Ends -->
    </div>
    <!-- Main Wrapper Ends -->
{{--@else--}}
{{--    <!-- Unauthorized Access Message -->--}}
{{--    <div class="section" role="main">--}}
{{--        <div class="card">--}}
{{--            <div class="card-body text-center">--}}
{{--                    <span class="titleup text-danger">--}}
{{--                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;--}}
{{--                        {{ trans('app.You Are Not Authorized to Access This Page.') }}--}}
{{--                    </span>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endif--}}
</body>

<!-- JQUERY SCRIPTS JS -->
<script src="{{ asset('plugins/hyperform/dist/hyperform.js') }}"></script>
<script>hyperform(window)</script>
<script src="{{ asset('js/vendors/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('plugins/date-picker/jquery-ui.js') }}"></script>

<!-- Input Mask -->
<script src="{{ asset('plugins/input-mask/input-mask.min.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>

<!-- Date Picker -->
<script src="{{ asset('plugins/date-picker/date-picker.js') }}"></script>

<!-- Bootstrap Bundle -->
<script src="{{ asset('js/vendors/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>

<!-- SweetAlert2 and Multi Select -->
{{--<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>--}}
<script src="{{ asset('plugins/multipleselect/multiple-select.js') }}"></script>
<script src="{{ asset('plugins/multipleselect/multi-select.js') }}"></script>

<!-- Moment.js -->
<script src="{{ asset('js/my_js_files/moment.js') }}"></script>

<!-- Print Button -->
<script src="{{ asset('plugins/print/dist/jQuery.print.min.js') }}"></script>

<!-- Custom Scripts -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/my_js_files/myjs.js') }}"></script>

<!-- CoreUI and Necessary Plugins -->
<script src="{{ asset('/assets/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('/assets/vendors/simplebar/js/simplebar.min.js') }}"></script>

@yield('scripts')
<script>
    function changeYear(year) {
        $.ajax({
            type: 'POST',
            url: '/change-year',
            data: { year: year, _token: "{{ csrf_token() }}" },
            success: function () {
                location.reload();
            },
            error: function (error) {
                console.error('Error changing year', error);
            }
        });
    }
    function changeMonth(month) {
        $.ajax({
            type: 'POST',
            url: '/change-month',
            data: { month: month, _token: "{{ csrf_token() }}" },
            success: function () {
                location.reload();
            },
            error: function (error) {
                console.error('Error changing month', error);
            }
        });
    }
</script>

</html>

