<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}"/>
</head>
<body>
@include('layouts.sidebar')
@include('layouts.header')

<div class="main with-sidebar">
<div class="page-shell">
<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-lg">

                <!-- Header -->
                <div class="card-header text-center">
                    User Registration
                    <div class="title-divider"></div>
                </div>

                <!-- Body -->
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('slecic.register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Create a secure password" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="card-footer text-center">
                    Already have an account?
                    <a href="{{ route('login.form') }}">Login</a>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>

</body>
</html>
