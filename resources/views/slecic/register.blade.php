<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #0f3d5e;
        }

        .card {
            border: none;
            border-radius: 14px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: none;
            padding-top: 30px;
            font-size: 20px;
            font-weight: 600;
            color: #0f3d5e;
        }

        .title-divider {
            width: 50px;
            height: 3px;
            background-color: #0f3d5e;
            margin: 10px auto 25px;
            border-radius: 2px;
        }

        .form-label {
            font-weight: 500;
            font-size: 14px;
            color: #0f3d5e;
        }

        .form-control {
            border-radius: 8px;
            padding: 11px 12px;
            font-size: 14px;
            border: 1px solid #d1d5db;
            color: #0f3d5e;
        }

        .form-control::placeholder {
            color: #6b7280;
        }

        .form-control:focus {
            border-color: #0f3d5e;
            box-shadow: 0 0 0 0.15rem rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            border-radius: 10px;
            padding: 11px;
            font-weight: 600;
            font-size: 15px;
            background-color: #0f3d5e;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #0f3d5e;
            transform: translateY(-1px);
        }

        .card-footer {
            background-color: transparent;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            padding: 16px;
            color: #0f3d5e;
        }

        .card-footer a {
            font-weight: 600;
            text-decoration: none;
            color: #0f3d5e;
        }
    </style>
</head>
<body>

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

</body>
</html>
