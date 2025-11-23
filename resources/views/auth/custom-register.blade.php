<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Registration User</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        .bg-register-image {
            background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: center;
            padding-top: 80px;
        }
        
        .logo-wrapper {
            text-align: center;
            padding: 20px;
            z-index: 2;
        }
        
        .logo-wrapper img {
            max-width: 70%;
            height: auto;
        }
        
        .logo-title {
            color: #fff;
            margin-top: 15px;
            font-weight: 700;
            font-size: 1.8rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .logo-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
            margin-top: 5px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        .pattern-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.8;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .input-group-text {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        
        .form-control-user {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        
        .border-left-0 {
            border-left: 0 !important;
        }
        
        .border-right-0 {
            border-right: 0 !important;
        }
        
        @media (max-width: 991.98px) {
            .bg-register-image {
                min-height: 200px;
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image">
                        <div class="pattern-overlay"></div>
                        <div class="logo-wrapper">
                            <!-- Replace with your actual logo -->
                            <img src="{{ asset('assets/img/undraw_posting_photo.svg') }}" alt="Logo" onerror="this.src='{{ asset('assets/img/undraw_rocket.svg') }}'; this.style.maxWidth='60%';">
                            <h1 class="logo-title">Organizer Management</h1>
                            <p class="logo-subtitle">Join our community and participate </p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create Account!</h1>
                            </div>
                            <form class="user" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fas fa-user text-primary"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control form-control-user border-left-0 @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                                    </div>
                                    @error('name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent border-right-0">
                                                <i class="fas fa-envelope text-primary"></i>
                                            </span>
                                        </div>
                                        <input type="email" class="form-control form-control-user border-left-0 @error('email') is-invalid @enderror"
                                            id="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}" required>
                                    </div>
                                    @error('email')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Hidden role input -->
                                <input type="hidden" name="role" value="peserta">
                                
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-transparent border-right-0">
                                                    <i class="fas fa-lock text-primary"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control form-control-user border-left-0 @error('password') is-invalid @enderror"
                                                id="password" name="password" placeholder="Password" required>
                                        </div>
                                        @error('password')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-transparent border-right-0">
                                                    <i class="fas fa-lock text-primary"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control form-control-user border-left-0"
                                                id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    <i class="fas fa-user-plus mr-2"></i> Daftar Akun
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('login') }}">Already have an account? Login!</a>
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

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>

</body>

</html>