<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    <title>403 Forbidden - Material Dashboard</title>

    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link href="/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link id="pagestyle" href="/assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
</head>

<body class="bg-gray-200">
<main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?auto=format&fit=crop&w=1950&q=80');">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container my-auto text-center">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="display-1 text-light">404</h1>
                    <h2 class="text-light mb-3">Oops! The page you're looking for doesn't exist.</h2>
                    <p class="mb-4 text-light">It might have been moved, deleted, or the URL could be incorrect.</p>
                    <a href="{{ route('attempts') }}" class="btn btn-outline-light btn-lg">← Back to Dashboard</a>
                </div>
            </div>
        </div>
{{--        <footer class="footer position-absolute bottom-2 py-2 w-100">--}}
{{--            <div class="container">--}}
{{--                <div class="row align-items-center justify-content-lg-between">--}}
{{--                    <div class="col-12 col-md-6 my-auto">--}}
{{--                        <div class="text-white text-center text-sm text-lg-start">--}}
{{--                            © <script>document.write(new Date().getFullYear())</script>,--}}
{{--                            made with <i class="fa fa-heart" aria-hidden="true"></i> by--}}
{{--                            <a href="https://www.creative-tim.com" class="font-weight-bold text-white" target="_blank">Creative Tim & UPDIVISION</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-12 col-md-6">--}}
{{--                        <ul class="nav nav-footer justify-content-center justify-content-lg-end">--}}
{{--                            <li class="nav-item"><a href="https://www.creative-tim.com" class="nav-link text-white">Creative Tim</a></li>--}}
{{--                            <li class="nav-item"><a href="https://www.creative-tim.com/presentation" class="nav-link text-white">About Us</a></li>--}}
{{--                            <li class="nav-item"><a href="https://www.creative-tim.com/blog" class="nav-link text-white">Blog</a></li>--}}
{{--                            <li class="nav-item"><a href="https://www.creative-tim.com/license" class="nav-link text-white">License</a></li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </footer>--}}
    </div>
</main>

<!-- Core JS Files -->
<script src="/assets/js/core/popper.min.js"></script>
<script src="/assets/js/core/bootstrap.min.js"></script>
<script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
    }
</script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="/assets/js/material-dashboard.min.js?v=3.0.0"></script>
</body>
</html>
