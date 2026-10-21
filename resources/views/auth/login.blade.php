<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/matrix-admin-master/assets/images/favicon.png') }}">
    <title>Login - {{ config('app.name', 'Nusa Jelajah') }}</title>
    <link href="{{ asset('backend/matrix-admin-master/dist/css/style.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --login-ink: #12323b;
            --login-teal: #137b8b;
            --login-muted: #71828a;
        }

        body.login-page {
            min-height: 100vh;
            margin: 0;
            color: var(--login-ink);
            background: #f4f8f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .login-page .main-wrapper,
        .login-page .auth-wrapper {
            min-height: 100vh;
        }

        .login-page .auth-wrapper {
            position: relative;
            overflow: hidden;
            padding: 32px 20px;
            background:
                linear-gradient(120deg, rgba(8, 39, 48, .64), rgba(8, 39, 48, .16)),
                url("{{ asset('images/Homepantai.jpg') }}") center / cover no-repeat;
        }

        .login-page .auth-wrapper::before {
            position: absolute;
            inset: 0;
            content: "";
            background: linear-gradient(90deg, rgba(8, 39, 48, .22), transparent 65%);
            pointer-events: none;
        }

        .login-page .auth-box {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            padding: 38px 40px 30px;
            border: 1px solid rgba(255, 255, 255, .72) !important;
            border-top: 0 !important;
            border-radius: 22px;
            background: rgba(255, 255, 255, .97) !important;
            box-shadow: 0 24px 70px rgba(6, 35, 43, .28);
        }

        .login-page .login-brand {
            display: inline-flex;
            width: 100px;
            height: 100px;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            color: #fff;
            background: linear-gradient(135deg, #137b8b, #1ba4a5);
            box-shadow: 0 8px 20px rgba(19, 123, 139, .24);
            font-size: 19px;
            font-weight: 800;
            letter-spacing: .04em;
        }

        .login-page .login-heading {
            margin: 18px 0 5px;
            color: var(--login-ink);
            font-size: 25px;
            font-weight: 750;
            letter-spacing: -.03em;
        }

        .login-page .login-intro {
            margin: 0;
            color: var(--login-muted);
            font-size: 14px;
        }

        .login-page .alert {
            border: 0;
            border-radius: 12px;
            font-size: 13px;
        }

        .login-page .input-group {
            margin-bottom: 16px !important;
        }

        .login-page .input-group-text {
            width: 48px;
            justify-content: center;
            border: 1px solid #dce7e9;
            border-right: 0;
            border-radius: 11px 0 0 11px;
            color: var(--login-teal) !important;
            background: #f0f7f7 !important;
        }

        .login-page .form-control {
            height: 50px;
            border: 1px solid #dce7e9;
            border-radius: 0 11px 11px 0;
            color: var(--login-ink);
            background: #fff;
            box-shadow: none;
            font-size: 14px;
        }

        .login-page .form-control:focus {
            z-index: 3;
            border-color: #56aab3;
            box-shadow: 0 0 0 3px rgba(19, 123, 139, .12);
        }

        .login-page .form-control::placeholder {
            color: #96a5aa;
        }

        .login-page .custom-control-label {
            color: #53666d !important;
            font-size: 13px;
            cursor: pointer;
        }

        .login-page .login-actions {
            gap: 12px;
            padding-top: 18px;
            border-top: 1px solid #edf1f2;
        }

        .login-page .login-actions .btn {
            min-height: 45px;
            padding: 10px 18px;
            border: 0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 650;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .login-page .login-actions .btn:hover {
            transform: translateY(-1px);
        }

        .login-page .login-register {
            color: var(--login-teal);
            background: #eaf5f5;
        }

        .login-page .login-register:hover {
            color: #0d5d68;
            background: #dcefee;
        }

        .login-page .login-submit {
            color: #fff;
            background: linear-gradient(135deg, #137b8b, #168e96);
            box-shadow: 0 7px 16px rgba(19, 123, 139, .2);
        }

        .login-page .login-submit:hover {
            color: #fff;
            box-shadow: 0 9px 20px rgba(19, 123, 139, .3);
        }

        .login-page .demo-login {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #edf1f2;
        }

        .login-page .demo-login small {
            margin-bottom: 10px;
            color: var(--login-muted) !important;
        }

        .login-page .demo-login .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .login-page .demo-login .btn {
            flex: 1 1 40%;
            border: 1px solid #dce7e9 !important;
            border-radius: 8px !important;
            color: #42616a !important;
            background: #fff;
            font-size: 12px;
        }

        .login-page .demo-login .btn:hover {
            border-color: #9bc9cd !important;
            color: var(--login-teal) !important;
            background: #f1f9f9;
        }

        .login-page .login-home {
            display: inline-block;
            margin-top: 22px;
            color: #71828a !important;
            font-size: 13px;
            text-decoration: none;
        }

        .login-page .login-home:hover {
            color: var(--login-teal) !important;
        }

        @media (max-width: 575.98px) {
            .login-page .auth-wrapper {
                padding: 18px 14px;
                background-position: center;
            }

            .login-page .auth-box {
                padding: 30px 22px 24px;
                border-radius: 18px;
            }

            .login-page .login-heading {
                font-size: 23px;
            }
        }
    </style>
</head>

<body class="login-page">
    <div class="main-wrapper">
        <main class="auth-wrapper d-flex no-block justify-content-center align-items-center">
            <div class="auth-box">
                <div id="loginform">
                    <div class="text-center p-t-20 p-b-20">
                        <span class="login-brand" aria-hidden="true">Terra Travel </span>
                        <h1 class="login-heading">{{ config('app.name', 'Nusa Jelajah') }}</h1>
                        <p class="login-intro">Masuk dan lanjutkan petualangan Anda.</p>
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

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="login-actions d-flex justify-content-between align-items-center">
                                        <a href="{{ route('register') }}" class="btn login-register">
                                            Buat akun
                                        </a>
                                        <button class="btn login-submit" type="submit">
                                            Masuk
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- {{-- Demo Accounts Quick Helper --}}
                    <div class="demo-login text-center">
                        <small class="text-muted d-block m-b-10">Uji coba cepat login (akun seeder):</small>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="admin@gmail.com" data-password="admin123">Admin</button>
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="tourguide@gmail.com" data-password="tourguide123">Tour Guide</button>
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="manager@gmail.com" data-password="manager123">Manager</button>
                            <button type="button" class="btn btn-outline-secondary text-white btn-demo" data-email="user@example.com" data-password="password">Customer</button>
                        </div>
                    </div> -->

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="login-home"><i class="fa fa-arrow-left m-r-5"></i> Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('backend/matrix-admin-master/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>
        $('[data-toggle="tooltip"]').tooltip();

        $('.btn-demo').on('click', function() {
            var email = $(this).data('email');
            var password = $(this).data('password');
            $('input[name="email"]').val(email);
            $('input[name="password"]').val(password);
        });
    </script>
</body>
</html>
