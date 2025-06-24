<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Kpi tizimi</title>
        <!-- plugins:css -->
        <link rel="stylesheet" href="/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="/assets/vendors/iconfonts/ionicons/dist/css/ionicons.css">
        <link rel="stylesheet" href="/assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css">
        <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
        <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.addons.css">
        <!-- <link rel="stylesheet" href="../../assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css" /> -->
        <link rel="stylesheet" href="/assets/datepicker/css/bootstrap-datepicker.css">
        <link rel="stylesheet" href="/assets/datepicker/css/bootstrap.css">
        <link rel="stylesheet" href="/assets/datepicker/css/bootstrap-responsive.css">
        <link rel="stylesheet" href="/assets/bower_components/chartist/dist/chartist.min.css">

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


        <!-- endinject -->
        <!-- plugin css for this page -->
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <link rel="stylesheet" href="/assets/css/shared/style.css">
        <!-- endinject -->
        <!-- Layout styles -->
        <link rel="stylesheet" href="/assets/css/demo_1/style.css">
        <!-- End Layout styles -->
        <link rel="stylesheet" href="/assets/css/custom.css">
        <link rel="shortcut icon" href="/assets/images/favicon.ico" />
        @stack('styles')
    </head>
    <body>
        <div class="container-scroller">
            <!-- partial:partials/_navbar.html -->
            @include('layouts.blocks.top_navbar')
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <!-- partial:partials/_sidebar.html -->
                @include('layouts.blocks.sidebar')
                <!-- partial -->
                <div class="main-panel" id="main-panel">
                    @yield('content')
                    <!-- partial:partials/_footer.html -->
                    @include('layouts.blocks.footer')
                    <!-- partial -->
                </div>
                <!-- main-panel ends -->
            </div>
            <!-- page-body-wrapper ends -->
        </div>
        <!-- container-scroller -->
        <!-- plugins:js -->
        <script src="/assets/vendors/js/vendor.bundle.base.js"></script>
        <script src="/assets/vendors/js/vendor.bundle.addons.js"></script>
        <script src="/assets/datepicker/js/jquery.js"></script>
        <script src="/assets/datepicker/js/bootstrap-datepicker.js"></script>
        <script src="/assets/datepicker/js/bootstrap.js"></script>

        <script src="/assets/vendors/chart.js/Chart.min.js"></script>
        <script src="/assets/bower_components/chartist/dist/chartist.min.js"></script>
        <script src="/assets/vendors/chart.js/Chart.min.js"></script>

        <!-- endinject -->
        <!-- Plugin js for this page-->
        <!-- End plugin js for this page-->
        <!-- inject:js -->
        <script src="/assets/js/shared/off-canvas.js"></script>
        <script src="/assets/js/shared/misc.js"></script>
        <!-- endinject -->
        <!-- Custom js for this page-->
        <script src="/assets/js/demo_1/dashboard.js"></script>
        <!-- End custom js for this page-->
        <script src="/assets/js/custom.js"></script>


{{--        <script src="/assets/js/shared/jquery.cookie.js" type="text/javascript"></script>--}}
        @stack('scripts')
    </body>
</html>
