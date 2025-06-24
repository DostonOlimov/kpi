<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>O'zagroinspeksiya KPI baholash tizimi</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="/assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="/assets/vendors/iconfonts/ionicons/dist/css/ionicons.css">
    <link rel="stylesheet" href="/assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/assets/vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="/assets/css/shared/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico"/>
</head>
<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
            <div class="row w-100">
                <div class="col-lg-4 mx-auto">
                    <div class="auto-form-wrapper">
                        <div class="d-flex justify-content-center">
                            <img style="width: 200px;" src="{{ url('/assets/images/logoNE.png') }}" alt="logo">
                        </div>
                        <form action="{{ route('authenticate') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="label">Username</label>
                                <div class="input-group">
                                    <input name="username" type="text" class="form-control" placeholder="Username" autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                          <i class="mdi mdi-check-circle-outline"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="label">Password</label>
                                <div class="input-group">
                                    <input name="password" type="password" class="form-control" placeholder="*********" autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                          <i class="mdi mdi-check-circle-outline"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary submit-btn btn-block">Kirish</button>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <p class="footer-text text-center">Copyright © {{ date('Y') }} O'ZAGROINSPEKSIYA. Barcha huquqlar himoyalangan.</p>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- plugins:js -->
<script src="/assets/vendors/js/vendor.bundle.base.js"></script>
<script src="/assets/vendors/js/vendor.bundle.addons.js"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="/assets/js/shared/off-canvas.js"></script>
<script src="/assets/js/shared/misc.js"></script>
<!-- endinject -->
{{--<script src="{{ ' }}assets/js/shared/jquery.cookie.js" type="text/javascript"></script>--}}
</body>
</html>
