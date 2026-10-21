<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/matrix-admin-master/assets/images/favicon.png') }}">
    <title>Register - {{ config('app.name', 'Terra Travel') }}</title>
    <link href="{{ asset('backend/matrix-admin-master/dist/css/style.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --register-ink: #12323b;
            --register-teal: #137b8b;
            --register-muted: #71828a;
        }

        body.register-page {
            min-height: 100vh;
            margin: 0;
            color: var(--register-ink);
            background: #f4f8f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .register-page .main-wrapper,
        .register-page .auth-wrapper {
            min-height: 100vh;
        }

        .register-page .auth-wrapper {
            position: relative;
            overflow: hidden;
            padding: 32px 20px;
            background:
                linear-gradient(120deg, rgba(8, 39, 48, .64), rgba(8, 39, 48, .16)),
                url("{{ asset('images/Homepantai.jpg') }}") center / cover no-repeat;
        }

        .register-page .auth-wrapper::before {
            position: absolute;
            inset: 0;
            content: "";
            background: linear-gradient(90deg, rgba(8, 39, 48, .22), transparent 65%);
            pointer-events: none;
        }

        .register-page .auth-box {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            padding: 32px 40px 26px;
            border: 1px solid rgba(255, 255, 255, .72) !important;
            border-top: 0 !important;
            border-radius: 22px;
            background: rgba(255, 255, 255, .97) !important;
            box-shadow: 0 24px 70px rgba(6, 35, 43, .28);
        }

        .register-page .register-brand {
            display: inline-flex;
            width: 100px;
            height: 100px;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            color: #fff;
            background: linear-gradient(135deg, #137b8b, #1ba4a5);
            box-shadow: 0 8px 20px rgba(19, 123, 139, .24);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: .04em;
        }

        .register-page .register-heading {
            margin: 14px 0 5px;
            color: var(--register-ink);
            font-size: 24px;
            font-weight: 750;
            letter-spacing: -.03em;
        }

        .register-page .register-intro {
            margin: 0;
            color: var(--register-muted);
            font-size: 14px;
        }

        .register-page .alert {
            border: 0;
            border-radius: 12px;
            font-size: 13px;
        }

        .register-page .input-group {
            margin-bottom: 13px !important;
        }

        .register-page .input-group-text {
            width: 48px;
            justify-content: center;
            border: 1px solid #dce7e9;
            border-right: 0;
            border-radius: 11px 0 0 11px;
            color: var(--register-teal) !important;
            background: #f0f7f7 !important;
        }

        .register-page .form-control {
            height: 48px;
            border: 1px solid #dce7e9;
            border-radius: 0 11px 11px 0;
            color: var(--register-ink);
            background: #fff;
            box-shadow: none;
            font-size: 14px;
        }

        .register-page .form-control:focus {
            z-index: 3;
            border-color: #56aab3;
            box-shadow: 0 0 0 3px rgba(19, 123, 139, .12);
        }

        .register-page .form-control::placeholder {
            color: #96a5aa;
        }

        .register-page .register-actions {
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #edf1f2;
        }

        .register-page .register-actions .btn {
            min-height: 44px;
            padding: 10px 16px;
            border: 0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 650;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .register-page .register-actions .btn:hover {
            transform: translateY(-1px);
        }

        .register-page .register-login {
            color: var(--register-teal);
            background: #eaf5f5;
        }

        .register-page .register-login:hover {
            color: #0d5d68;
            background: #dcefee;
        }

        .register-page .register-submit {
            color: #fff;
            background: linear-gradient(135deg, #137b8b, #168e96);
            box-shadow: 0 7px 16px rgba(19, 123, 139, .2);
        }

        .register-page .register-submit:hover {
            color: #fff;
            box-shadow: 0 9px 20px rgba(19, 123, 139, .3);
        }

        .register-page .register-home {
            display: inline-block;
            margin-top: 18px;
            color: var(--register-muted) !important;
            font-size: 13px;
            text-decoration: none;
        }

        .register-page .register-home:hover {
            color: var(--register-teal) !important;
        }

        @media (max-width: 575.98px) {
            .register-page .auth-wrapper {
                padding: 18px 14px;
                background-position: center;
            }

            .register-page .auth-box {
                padding: 28px 22px 22px;
                border-radius: 18px;
            }

            .register-page .register-heading {
                font-size: 22px;
            }
        }
    </style>
</head>

<body class="register-page">
    <div class="main-wrapper">
        <main class="auth-wrapper d-flex no-block justify-content-center align-items-center">
            <div class="auth-box">
                <div>
                    <div class="text-center p-t-20 p-b-20">
                        <span class="register-brand" aria-hidden="true">Terra Travel</span>
                        <h1 class="register-heading">Buat akun baru</h1>
                        <p class="register-intro">Bergabung bersama {{ config('app.name', 'Nusa Jelajah') }}</p>
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

                    <form class="form-horizontal m-t-20" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="row p-b-20">
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Nama Lengkap" aria-label="Nama" aria-describedby="basic-addon1" required autofocus>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2"><i class="ti-email"></i></span>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Alamat Email" aria-label="Email" aria-describedby="basic-addon2" required>
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon3"><i class="fa fa-phone"></i></span>
                                    </div>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg @error('phone') is-invalid @enderror" placeholder="Nomor Telepon / WhatsApp" aria-label="Phone" aria-describedby="basic-addon3">
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon4"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Kata Sandi (min. 8 karakter)" aria-label="Password" aria-describedby="basic-addon4" required autocomplete="new-password">
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon5"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Konfirmasi Kata Sandi" aria-label="Confirm Password" aria-describedby="basic-addon5" required autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="register-actions d-flex justify-content-between align-items-center">
                                        <a href="{{ route('login') }}" class="btn register-login">
                                            Kembali ke Login
                                        </a>
                                        <button class="btn register-submit" type="submit">
                                            Buat akun
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="register-home"><i class="fa fa-arrow-left m-r-5"></i> Kembali ke Beranda</a>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('backend/matrix-admin-master/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('backend/matrix-admin-master/assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script>
        $('[data-toggle="tooltip"]').tooltip();
    </script>
</body>
</html>
