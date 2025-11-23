
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Kode - Monoframe Studio</title>
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/auth-common.css') }}" rel="stylesheet">
</head>
<body class="animated-gradient">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="auth-card card overflow-hidden">
                    <div class="p-0 card-body">
                        <div class="row no-gutters">
                            <div class="col-lg-5 d-none d-lg-block brand-section animated-gradient">
                                <div class="brand-overlay"></div>
                                <div class="logo-wrapper">
                                    <img src="{{ asset('assets/img/monoframe.png') }}" alt="Monoframe Logo" class="logo-img animate__animated animate__fadeIn">
                                    <h1 class="logo-title animate__animated animate__fadeInUp">Monoframe</h1>
                                    <p class="logo-subtitle animate__animated animate__fadeInUp">Management System</p>
                                </div>
                            </div>
                            <div class="col-lg-7 auth-form-container">
                                <div class="auth-form">
                                    <div class="mb-5 text-center">
                                        <img src="{{ asset('assets/img/monoframe.png') }}" alt="Monoframe Logo" class="d-lg-none mobile-logo">
                                        <h2 class="heading">Verifikasi Kode</h2>
                                        <p class="text-muted">Masukkan kode verifikasi 6 digit yang telah dikirim ke email Anda.</p>
                                    </div>

                                    @if(session('status'))
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                                        </div>
                                    @endif

                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.verify.code') }}">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ old('email', request('email')) }}">
                                        <div class="form-group">
                                            <label for="code">Kode Verifikasi</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" maxlength="6" required autofocus pattern="[0-9]{6}">
                                            @error('code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="d-flex flex-column mt-4">
                                            <button type="submit" class="btn btn-primary btn-block btn-login py-2">
                                                <i class="fas fa-check mr-2"></i> Verifikasi & Lanjutkan
                                            </button>
                                            <a href="{{ route('password.request') }}" class="btn btn-light btn-block btn-login py-2 mt-3">
                                                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Lupa Password
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
