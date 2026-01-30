<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SLECIC | APARA System Login</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            font-family: "Segoe UI", sans-serif;
        }

        .login-wrapper {
            height: 100vh;
        }

        /* LEFT IMAGE PANEL */
        .left-panel {
            background: url("{{ asset('assets/images/apara-bg.jpg') }}") no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .left-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 61, 94, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            color: #fff;
        }

        .left-overlay h1 {
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .left-overlay p {
            font-size: 15px;
            opacity: 0.95;
        }

        /* RIGHT LOGIN PANEL */
        .login-card {
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        .brand-title {
            font-weight: 700;
            color: #0f3d5e;
        }

        .brand-subtitle {
            font-size: 14px;
            color: #6c757d;
        }

        .btn-login {
            background-color: #0f3d5e;
            color: #fff;
        }

        .btn-login:hover {
            background-color: #0b2f49;
        }

        .footer-text {
            font-size: 12px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .left-panel {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid login-wrapper">
    <div class="row h-100">

        <!-- LEFT IMAGE SIDE -->
        <div class="col-md-7 d-none d-md-block left-panel">
            <div class="left-overlay">
                <div>
                    <h1>SLECIC</h1>
                    <p>APARA Guarantee Management System</p>
                    <p class="mt-3 small">
                        Secure • Reliable • Government Approved
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT LOGIN SIDE -->
        <div class="col-md-5 d-flex align-items-center justify-content-center bg-light">

            <div class="card login-card p-4 w-75">

                <div class="text-center mb-4">
                    <h3 class="brand-title">System Login</h3>
                    <div class="brand-subtitle">
                        Enter your credentials to continue
                    </div>
                </div>

                {{-- Error Message --}}
                @if($errors->any())
                    <div class="alert alert-danger text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-user"></i>
                            </span>
                            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    </div>

                    <!-- Forgot Password -->
                    <div class="mb-3 text-end">
                        <a href="#" class="text-decoration-none small" style="color:#0f3d5e;">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-login">
                            <i class="fa fa-sign-in-alt me-1"></i> Login
                        </button>
                    </div>

                </form>

                <hr>

                <div class="text-center footer-text">
                    © {{ date('Y') }} Sri Lanka Export Credit Insurance Corporation  
                    <br> APARA System
                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>
