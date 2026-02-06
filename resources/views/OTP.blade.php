<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Your CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="otp-wrapper">

    <div class="otp-card text-center">

        <h4 class="mb-2">OTP Verification</h4>
        <p class="text-muted">Enter the 6-digit OTP sent to your email</p>

        <form action="/OTP" method="POST">
            <!-- Laravel CSRF -->
            <!-- @csrf -->

            <div class="d-flex justify-content-center mb-3">
                <input type="text" maxlength="1" class="form-control otp-input" required>
                <input type="text" maxlength="1" class="form-control otp-input" required>
                <input type="text" maxlength="1" class="form-control otp-input" required>
                <input type="text" maxlength="1" class="form-control otp-input" required>
                <input type="text" maxlength="1" class="form-control otp-input" required>
                <input type="text" maxlength="1" class="form-control otp-input" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Verify OTP
            </button>

            <div class="mt-3">
                <a href="#" class="text-decoration-none">Resend OTP</a>
            </div>
        </form>

    </div>

</div>

<script>
    const inputs = document.querySelectorAll(".otp-input");

    inputs.forEach((input, index) => {

        input.addEventListener("input", () => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && input.value === "" && index > 0) {
                inputs[index - 1].focus();
            }
        });

    });
</script>

</body>
</html>
