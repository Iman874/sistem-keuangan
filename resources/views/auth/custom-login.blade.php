<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Monoframe">

    <title>Login - Monoframe Studio</title>

    <!-- Custom fonts -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Custom styles -->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/auth.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/auth-common.css') }}" rel="stylesheet">

    <style>
        /* Additional custom styles for login page */
        .login-card {
            box-shadow: 0 8px 32px 0 var(--shadow-color);
        }
    </style>
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
                                        <h2 class="heading">Welcome Back</h2>
                                        <p class="text-muted">Sign in to your account</p>
                                    </div>

                                    @if(session('error'))
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                                        </div>
                                    @endif

                                    @if(session('status'))
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}" class="login-form-content">
                                        @csrf
                                        <div class="form-group">
                                            <label for="credential"><i class="fas fa-user-circle mr-1"></i>Email or Username</label>
                                            <div class="input-icon-container">
                                                <i class="fas fa-envelope input-icon"></i>
                                                <input type="text" class="form-control icon-input @error('credential') is-invalid @enderror"
                                                    id="credential" name="credential" placeholder="Enter your email or username"
                                                    value="{{ old('credential') }}" required autofocus>
                                                @error('credential')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password"><i class="fas fa-lock mr-1"></i>Password</label>
                                            <div class="input-icon-container">
                                                <i class="fas fa-key input-icon"></i>
                                                <input type="password" class="form-control icon-input @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Enter your password" required>
                                                @error('password')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group d-flex justify-content-between align-items-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                                                <label class="custom-control-label" for="remember_me">Remember Me</label>
                                            </div>
                                            <a href="{{ route('password.request') }}" class="small text-primary font-weight-bold">Lupa Password?</a>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block btn-auth py-2 mt-4">
                                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                                        </button>
                                    </form>
                                    <div class="mt-4 text-center">
                                        <p class="text-muted small">© 2025 Monoframe Management System</p>
                                    </div>
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

    <!-- Animation library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <script>
        // Add smooth animations
        $(document).ready(function() {
            // Fade in elements with a slight delay
            $('.login-form-content').hide().fadeIn(800);

            // Focus effect for input fields
            $('.form-control').focus(function() {
                $(this).parent().addClass('focused');
            }).blur(function() {
                $(this).parent().removeClass('focused');
            });

            // Button press effect
            $('.btn-login').on('mousedown', function() {
                $(this).css('transform', 'scale(0.98)');
            }).on('mouseup mouseleave', function() {
                $(this).css('transform', 'translateY(-2px)');
            });
        });
    </script>

    <script>
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable keyboard shortcuts for developer tools
        document.addEventListener('keydown', function(e) {
            // Prevent F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            if (
                e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                (e.ctrlKey && e.keyCode === 85) // Ctrl+U
            ) {
                e.preventDefault();
                return false;
            }
        });

        // Disable drag and drop for images
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
                return false;
            }
        });

        // Display a message when right-clicking is attempted (optional)
        document.addEventListener('contextmenu', function() {
            // You can uncomment this if you want to show a message
            // alert('Right-clicking is disabled on this website.');
        });
    </script>
</body>
</html>