<!DOCTYPE html>
<html dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/matrix-admin-master/assets/images/favicon.png') }}">
    <title>@yield('title', 'Dashboard Admin') - {{ config('app.name', 'Matrix Admin') }}</title>
    <!-- Custom CSS -->
    <link href="{{ asset('backend/matrix-admin-master/assets/libs/flot/css/float-chart.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/matrix-admin-master/dist/css/style.min.css') }}" rel="stylesheet">
    @stack('styles')
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <!-- Main wrapper -->
    <div id="main-wrapper">
        <!-- Topbar header -->
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <!-- Sidebar toggle for mobile -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                        <i class="ti-menu ti-close"></i>
                    </a>
                    <!-- Logo -->
                    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                        <b class="logo-icon p-l-10">
                            <img src="{{ asset('backend/matrix-admin-master/assets/images/logo-icon.png') }}" alt="homepage" class="light-logo" />
                        </b>
                        <span class="logo-text">
                            <span class="text-white font-weight-bold">Dashboard Admin</span>
                        </span>
                    </a>
                    <!-- Mobile toggle -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti-more"></i>
                    </a>
                </div>

                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                    <!-- Left nav -->
                    <ul class="navbar-nav float-left mr-auto">
                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar">
                                <i class="mdi mdi-menu font-24"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link waves-effect waves-light" target="_blank" title="Lihat Website">
                                <i class="mdi mdi-web font-20 m-r-5"></i>
                                <span class="d-none d-md-inline">Lihat Web</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Right nav -->
                    <ul class="navbar-nav float-right">
                        <!-- User profile -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ asset('backend/matrix-admin-master/assets/images/users/1.jpg') }}" alt="user" class="rounded-circle" width="31">
                                <span class="text-white m-l-5 font-14">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated">
                                <div class="p-15 border-bottom bg-light">
                                    <h6 class="m-b-0 font-weight-bold">{{ Auth::user()->name ?? 'Admin' }}</h6>
                                    <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                                </div>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="ti-user m-r-5 m-l-5"></i> Profil Akun
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-power-off m-r-5 m-l-5"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left Sidebar -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav" class="p-t-30">
                        <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'selected' : '' }}">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                                <i class="mdi mdi-view-dashboard"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>

                        @if (Route::has('admin.packages.index'))
                        <li class="sidebar-item {{ request()->routeIs('admin.packages.*') ? 'selected' : '' }}">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.packages.index') }}" aria-expanded="false">
                                <i class="mdi mdi-airplane-takeoff"></i>
                                <span class="hide-menu">Paket Wisata</span>
                            </a>
                        </li>
                        @endif

                        @if (Route::has('admin.bookings.index'))
                        <li class="sidebar-item {{ request()->routeIs('admin.bookings.*') ? 'selected' : '' }}">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.bookings.index') }}" aria-expanded="false">
                                <i class="mdi mdi-receipt"></i>
                                <span class="hide-menu">Pemesanan</span>
                            </a>
                        </li>
                        @endif

                        @if (Route::has('admin.destinations.index'))
                        <li class="sidebar-item {{ request()->routeIs('admin.destinations.*') ? 'selected' : '' }}">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.destinations.index') }}" aria-expanded="false">
                                <i class="mdi mdi-map-marker"></i>
                                <span class="hide-menu">Destinasi</span>
                            </a>
                        </li>
                        @endif

                        @if (Route::has('admin.promos.index'))
                        <li class="sidebar-item {{ request()->routeIs('admin.promos.*') ? 'selected' : '' }}">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.promos.index') }}" aria-expanded="false">
                                <i class="mdi mdi-tag"></i>
                                <span class="hide-menu">Kode Promo</span>
                            </a>
                        </li>
                        @endif

                        @if (Route::has('admin.reports.index'))
                        <li class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'selected' : '' }}">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.reports.index') }}" aria-expanded="false">
                                <i class="mdi mdi-chart-bar"></i>
                                <span class="hide-menu">Laporan Keuangan</span>
                            </a>
                        </li>
                        @endif

                        @if (Route::has('admin.users.index'))
                        <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'selected' : '' }}">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.users.index') }}" aria-expanded="false">
                                <i class="mdi mdi-account-multiple"></i>
                                <span class="hide-menu">Kelola Pengguna</span>
                            </a>
                        </li>
                        @endif

                        <li class="sidebar-item border-top border-secondary m-t-15 p-t-15">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link text-danger" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="mdi mdi-power"></i>
                                <span class="hide-menu">Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Page wrapper -->
        <div class="page-wrapper">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">@yield('header', 'Dashboard')</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">@yield('header', 'Dashboard')</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Container fluid -->
            <div class="container-fluid">
                {{-- Flash Notifications --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal!</strong> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-b-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="footer text-center">
                &copy; {{ date('Y') }} {{ config('app.name', 'Nusa Jelajah') }}. Template by <a href="https://wrappixel.com" target="_blank">Matrix-Admin</a>.
            </footer>
        </div>
    </div>

    @push('scripts')
<script>
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function () {
        history.pushState(null, '', location.href);
    });
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) location.reload();
    });
</script>
@endpush

    <!-- Required js -->
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/extra-libs/sparkline/sparkline.js') }}"></script>
    <!-- Wave Effects -->
    <script src="{{ asset('backend/matrix-admin-master/dist/js/waves.js') }}"></script>
    <!-- Menu sidebar -->
    <script src="{{ asset('backend/matrix-admin-master/dist/js/sidebarmenu.js') }}"></script>
    <!-- Custom JavaScript -->
    <script src="{{ asset('backend/matrix-admin-master/dist/js/custom.min.js') }}"></script>
    @stack('scripts')
</body>
</html>