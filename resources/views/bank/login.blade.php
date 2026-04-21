<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $bankBrand['display_name'] ?? 'Bank Portal' }} Login - APARA System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>

<div class="container-fluid login-wrapper">
    <div class="row h-100">
        <div class="col-lg-7 d-none d-lg-block" style="background: linear-gradient(135deg, #102b44 0%, #26415f 60%, #3f648d 100%); position: relative;">
            <div class="left-overlay" style="background: rgba(10, 26, 43, 0.22);">
                <div class="text-start" style="max-width: 520px;">
                    <img src="{{ asset('images/logo-01.png') }}" alt="APARA Logo" style="width: 600px; max-width: 100%; height: auto;">
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-flex align-items-center justify-content-center" style="background: linear-gradient(180deg, #eef4fa 0%, #dde8f4 100%);">
            <div class="card login-card p-4 p-lg-5 w-100" style="max-width: 470px;">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <img src="{{ $bankBrand['logo'] ?? asset('images/logo.png') }}" alt="{{ $bankBrand['name'] ?? 'Bank' }} Logo" style="width: 72px; height: 72px; object-fit: contain;">
                    </div>
                    <h3 class="brand-title mb-2">{{ $bankBrand['name'] ?? 'Bank' }} Login</h3>
                    <p class="brand-subtitle mb-0">Use your bank-issued account to access the {{ $bankBrand['display_name'] ?? 'bank' }} dashboard.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="portal" value="bank">
                    <input type="hidden" name="bank_brand" value="{{ $bankBrand['slug'] ?? '' }}">

                    <div class="mb-3">
                        <label class="form-label">Bank Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-building-columns"></i></span>
                            <select name="bank_id" class="form-control" required>
                                <option value="">Select registered bank</option>
                                @foreach(($bankOptions ?? []) as $bankOption)
                                    <option value="{{ $bankOption['id'] }}" @selected(old('bank_id', $selectedBankId ?? null) == $bankOption['id'])>
                                        {{ $bankOption['bank_name'] }}@if(!empty($bankOption['branch_name'])) - {{ $bankOption['branch_name'] }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="Enter bank email" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-login">
                            <i class="fa fa-sign-in-alt me-2"></i>Sign In to Bank Portal
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <div class="small text-muted mt-2">Bank login accounts are created from the SLECIC bank registration side.</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
