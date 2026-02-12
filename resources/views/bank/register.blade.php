<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"/>

    <style>

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
