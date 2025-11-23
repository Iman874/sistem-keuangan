<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Monoframe">

    <title>Reset Password - Monoframe Studio</title>

    <!-- Custom fonts -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Custom styles -->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/auth.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="login-card card">
                    <div class="p-0 card-body">
                        <div class="row">
                            <div class="col-lg-5 d-none d-lg-block photobooth-banner">
                                <div class="logo-wrapper">
                                    <img src="{{ asset('assets/img/logo.png') }}" alt="Monoframe Logo" class="logo-img">
                                    <h1 class="logo-title">Monoframe</h1>
                                    <p class="logo-subtitle">Management System</p>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="login-form">
                                    <div class="mb-4 text-center">
                                        <img src="{{ asset('assets/img/monoframe.png') }}" alt="Monoframe Logo" class="d-lg-none mobile-logo">
                                        <h2 class="heading">Reset Password</h2>
                                        <p class="text-muted">Buat password baru untuk akun Anda</p>
                                    </div>

                                    @if (session('status'))
                                        <div class="alert alert-success">{{ session('status') }}</div>
                                    @endif
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.reset.custom') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" 
                                                value="{{ old('email', $email) }}" required readonly>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <input type="hidden" name="email" value="{{ $email }}">
                                        <div class="form-group">
                                            <label for="password">Password Baru</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password" required>
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation" name="password_confirmation" required>
                                            @error('password_confirmation')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="d-flex flex-column mt-4">
                                            <button type="submit" class="btn btn-primary btn-block btn-login py-2">
                                                <i class="fas fa-save mr-2"></i> Reset Password
                                            </button>
                                            <a href="{{ route('login') }}" class="btn btn-light btn-block btn-login py-2 mt-3">
                                                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Login
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

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
