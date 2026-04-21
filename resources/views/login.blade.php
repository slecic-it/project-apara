<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>

<div class="container-fluid login-wrapper">
    <div class="row h-100">

        <!-- LEFT IMAGE SIDE -->
        <div class="col-md-7 d-none d-md-block left-panel">
            <div class="left-overlay">
                <div>
                    <img src="{{ asset('images/logo-01.png') }}" alt="Logo" class="mb-3" style="width: 400px;">
                    <p></p>
                    <p class="mt-3 small">
                        
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT LOGIN SIDE -->
        <div class="col-md-5 d-flex align-items-center justify-content-center bg-light">

            <div class="card login-card p-4 w-75">

                <div class="text-center mb-4">
                    <h3 class="brand-title">Login</h3>
                    <div class="brand-subtitle">
                        
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
                            <i class="fa fa-sign-in-alt me-1"></i> Next
                        </button>
                    </div>

                </form>

                <hr>

                <div class="text-center mb-3">
                    <a href="{{ route('bank.login.form') }}" class="text-decoration-none small" style="color:#102b44;">
                        Bank login
                    </a>
                </div>

                <div class="text-center footer-text brand-subtitle">
                    © {{ date('Y') }} Sri Lanka Export Credit Insurance Corporation  
                    <br>
                </div>

            </div>

        </div>

    </div>
</div>

</body>
</html>
