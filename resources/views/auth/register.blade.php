<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/matrix-admin-master/assets/images/favicon.png') }}">
    <title>Register - {{ config('app.name', 'Nusa Jelajah') }}</title>
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

        <!-- Register box -->
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark" style="min-height: 100vh;">
            <div class="auth-box bg-dark border-top border-secondary">
                <div>
                    <div class="text-center p-t-20 p-b-20">
                        <span class="db">
                            <img src="{{ asset('backend/matrix-admin-master/assets/images/logo.png') }}" alt="logo" />
                        </span>
                        <h4 class="text-white m-t-15 font-weight-bold">Daftar Akun Baru</h4>
                        <p class="text-muted text-small">Bergabung bersama {{ config('app.name', 'Nusa Jelajah') }}</p>
                    </div>

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

                    <!-- Form Register -->
                    <form class="form-horizontal m-t-20" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="row p-b-20">
                            <div class="col-12">
                                <!-- Nama Lengkap -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Nama Lengkap" aria-label="Nama" aria-describedby="basic-addon1" required autofocus>
                                </div>

                                <!-- Email -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-danger text-white" id="basic-addon2"><i class="ti-email"></i></span>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Alamat Email" aria-label="Email" aria-describedby="basic-addon2" required>
                                </div>

                                <!-- Telepon / WhatsApp -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white" id="basic-addon3"><i class="fa fa-phone"></i></span>
                                    </div>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg @error('phone') is-invalid @enderror" placeholder="Nomor Telepon / WhatsApp" aria-label="Phone" aria-describedby="basic-addon3">
                                </div>

                                <!-- Password -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning text-white" id="basic-addon4"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Kata Sandi (min. 8 karakter)" aria-label="Password" aria-describedby="basic-addon4" required autocomplete="new-password">
                                </div>

                                <!-- Konfirmasi Password -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-info text-white" id="basic-addon5"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Konfirmasi Kata Sandi" aria-label="Confirm Password" aria-describedby="basic-addon5" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="row border-top border-secondary">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="p-t-20 d-flex justify-content-between align-items-center">
                                        <a href="{{ route('login') }}" class="btn btn-success">
                                            <i class="fa fa-sign-in-alt m-r-5"></i> Back to Login
                                        </a>
                                        <button class="btn btn-info" type="submit">
                                            <i class="fa fa-check m-r-5"></i> Sign Up
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

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
    </script>
</body>
</html>
