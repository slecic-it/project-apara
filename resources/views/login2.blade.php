@include('layouts.header')
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

                <div class="text-left mb-4">
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
                        <!-- <label class="form-label">Email</label> -->
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-user"></i>
                            </span>
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <!-- <label class="form-label">Password</label>  -->
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                    </div>

                    <!-- Forgot Password -->
                    <div class="mb-3 text-left">
                        <a href="#" class="text-decoration-none small" style="color:#0f3d5e;">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <div align="right" class="mt-3">
                        <button type="submit" class="btn btn-login" style="height:40px; width:150px" >
                            <i class="fa fa-sign-in-alt me-1"></i> Next
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
</div>

</body>
