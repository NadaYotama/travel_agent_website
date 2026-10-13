<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/matrix-admin-master/assets/images/favicon.png') }}">
    <title>Login - {{ config('app.name', 'Nusa Jelajah') }}</title>
    <!-- Custom CSS -->
    <link href="{{ asset('backend/matrix-admin-master/dist/css/style.min.css') }}" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="main-wrapper">
        <!-- Preloader -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>

        <!-- Login box -->
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark" style="min-height: 100vh;">
            <div class="auth-box bg-dark border-top border-secondary">
                <div id="loginform">
                    <div class="text-center p-t-20 p-b-20">
                        <span class="db">
                            <img src="{{ asset('backend/matrix-admin-master/assets/images/logo.png') }}" alt="logo" />
                        </span>
                        <h4 class="text-white m-t-15 font-weight-bold">{{ config('app.name', 'Nusa Jelajah') }}</h4>
                        <p class="text-muted text-small">Silakan masuk ke akun Anda</p>
                    </div>

                    {{-- Pesan Status / Session --}}
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Alert Error Validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="m-b-0 p-l-20">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Login -->
                    <form class="form-horizontal m-t-20" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="row p-b-20">
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white" id="basic-addon1"><i class="ti-email"></i></span>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email Address" aria-label="Email" aria-describedby="basic-addon1" required autocomplete="email" autofocus>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning text-white" id="basic-addon2"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" aria-label="Password" aria-describedby="basic-addon2" required autocomplete="current-password">
                                </div>

                                <div class="custom-control custom-checkbox mr-sm-2 mb-2">
                                    <input type="checkbox" name="remember" class="custom-control-input" id="customControlAutosizing">
                                    <label class="custom-control-label text-white" for="customControlAutosizing">Remember me</label>
                                </div>
                            </div>
                        </div>

                        <div class="row border-top border-secondary">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="p-t-20 d-flex justify-content-between align-items-center">
                                        <a href="{{ route('register') }}" class="btn btn-info">
                                            <i class="fa fa-user-plus m-r-5"></i> Register
                                        </a>
                                        <button class="btn btn-success" type="submit">
                                            <i class="fa fa-sign-in-alt m-r-5"></i> Login
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Demo Accounts Quick Helper --}}
                    <div class="border-top border-secondary p-t-15 m-t-10 text-center">
                        <small class="text-muted d-block m-b-10">Uji coba cepat login (akun seeder):</small>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="admin@example.com" data-password="password">Admin</button>
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="agent@example.com" data-password="password">Agen</button>
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="vendor@example.com" data-password="password">Vendor</button>
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="user@example.com" data-password="password">Customer</button>
                        </div>
                    </div>

                    <div class="text-center p-t-15">
                        <a href="{{ route('home') }}" class="text-muted"><i class="fa fa-arrow-left m-r-5"></i> Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required js -->
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>
        $('[data-toggle="tooltip"]').tooltip();
        $(".preloader").fadeOut();

        // Helper untuk mengisi otomatis form demo login
        $('.btn-demo').on('click', function() {
            var email = $(this).data('email');
            var password = $(this).data('password');
            $('input[name="email"]').val(email);
            $('input[name="password"]').val(password);
        });
    </script>
</body>
</html>
