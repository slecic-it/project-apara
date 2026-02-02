<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 14px;
            border: none;
            background-color: #ffffff;
        }

        .card-title {
            font-weight: 600;
            letter-spacing: 0.4px;
            color: #0f3d5e;
        }

        .title-divider {
            width: 60px;
            height: 3px;
            background-color: #0f3d5e;
            margin: 10px auto 25px;
            border-radius: 2px;
        }

        .form-label {
            font-weight: 500;
            color: #0f3d5e;
            font-size: 14px;
        }

        .form-control {
            border-radius: 8px;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-control:focus {
            border-color: #0f3d5e;
            box-shadow: 0 0 0 0.15rem rgba(15, 61, 94, 0.25);
        }

        .btn-primary {
            border-radius: 10px;
            padding: 11px;
            font-weight: 600;
            font-size: 15px;
            background-color: #0f3d5e;
            border: none;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0c2f48;
            transform: translateY(-1px);
        }

        .card-footer {
            background-color: transparent;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            padding: 16px;
        }

        .card-footer a {
            font-weight: 600;
            text-decoration: none;
            color: #0f3d5e;
        }

        .footer-text {
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-7">
            <div class="card shadow-lg">

                <!-- Card Body -->
                <div class="card-body p-4 p-lg-5">
                    <h4 class="card-title text-center">Bank Registration</h4>
                    <div class="title-divider"></div>

                    <form action="{{ route('bank.register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="e.g. National Bank" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Branch Name</label>
                            <input type="text" name="branch_name" class="form-control" placeholder="e.g. Colombo Main Branch" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Branch Code</label>
                            <input type="text" name="branch_code" class="form-control" placeholder="e.g. 0012" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIC / ID Number</label>
                            <input type="text" name="nic" class="form-control" placeholder="e.g. 200012345678" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" name="contact" class="form-control" placeholder="e.g. 0771234567" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. branch@email.com">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Register Bank
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Card Footer -->
                <div class="card-footer text-center">
                    Already have an account?
                    <a href="{{ route('login.form') }}">Login</a>
                </div>

            </div>

            <p class="text-center footer-text mt-3">
                © 2026 Bank Management System. All rights reserved.
            </p>
        </div>
    </div>
</div>

</body>
</html>
