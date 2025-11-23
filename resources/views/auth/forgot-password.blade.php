<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Monoframe">

    <title>Lupa Password - Monoframe Studio</title>

    <!-- Custom fonts -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Custom styles -->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/auth-common.css') }}" rel="stylesheet">
    
    <!-- Animation library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
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
                                        <h2 class="heading">Lupa Password</h2>
                                        <p class="text-muted">Masukkan alamat email Anda untuk melanjutkan proses reset password</p>
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

                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" 
                                                value="{{ old('email') }}" required autofocus>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        
                                        <div class="d-flex flex-column mt-4">
                                            <button type="submit" class="btn btn-primary btn-block btn-login py-2">
                                                <i class="fas fa-paper-plane mr-2"></i> Lanjutkan
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
