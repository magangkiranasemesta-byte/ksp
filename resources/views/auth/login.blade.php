<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Equipment Maintenance System</title>

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>

    <div class="auth-wrapper">

        <!-- ===================================================== -->
        <!-- SISI KIRI: BRANDING PERUSAHAAN -->
        <!-- ===================================================== -->

        <div class="auth-brand-side">

            <div class="brand-header">

                <div class="brand-icon">
                    MX
                </div>

                <div class="brand-name">
                    Maintenance X
                </div>

            </div>


            <div class="brand-body">

                <span class="system-badge">
                    Equipment Maintenance System
                </span>

                <h1 class="brand-title">
                    Enterprise Maintenance Operations
                </h1>

                <p class="brand-description">
                    Silakan masuk untuk mengelola tiket,
                    inventaris suku cadang, serta jadwal
                    pemeliharaan rutin peralatan perusahaan.
                </p>

            </div>


            <div class="brand-footer">

                &copy; {{ date('Y') }}
                PT Equipment Maintenance System.
                All rights reserved.

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- SISI KANAN: FORM LOGIN -->
        <!-- ===================================================== -->

        <div class="auth-form-side">

            <div class="form-container">


                <!-- HEADER -->
                <div class="form-header">

                    <h2 class="form-title">
                        Welcome Back
                    </h2>

                    <p class="form-subtitle">
                        Please enter your credentials to access
                        your account
                    </p>

                </div>


                <!-- ================================================= -->
                <!-- FORM LOGIN -->
                <!-- ================================================= -->

                <form
                    action="{{ route('login.store') }}"
                    method="POST"
                >

                    @csrf


                    <!-- ================================================= -->
                    <!-- USERNAME / EMAIL -->
                    <!-- ================================================= -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="login"
                        >
                            Username or Email
                        </label>


                        <div class="input-box">

                            <input
                                type="text"
                                name="login"
                                id="login"
                                value="{{ old('login') }}"
                                placeholder="Enter username or email"
                                required
                                autofocus
                                autocomplete="username"
                            >

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- PASSWORD -->
                    <!-- ================================================= -->

                    <div class="form-group">

                        <label
                            class="form-label"
                            for="login_password"
                        >
                            Password
                        </label>


                        <div class="input-box">

                            <input
                                type="password"
                                name="password"
                                id="login_password"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >


                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword(
                                    'login_password',
                                    this
                                )"
                            >
                                Show
                            </button>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- ERROR MESSAGE -->
                    <!-- ================================================= -->

                    @if ($errors->any())

                        <div class="error-message">

                            {{ $errors->first() }}

                        </div>

                    @endif


                    <!-- ================================================= -->
                    <!-- LOGIN BUTTON -->
                    <!-- ================================================= -->

                    <button
                        type="submit"
                        class="btn-primary"
                        style="margin-top: 10px;"
                    >
                        Sign In
                    </button>

                </form>


                <!-- ================================================= -->
                <!-- REGISTER -->
                <!-- ================================================= -->

                <div class="auth-redirect">

                    Don't have an account?

                    <a href="{{ route('register') }}">
                        Register Here
                    </a>

                </div>


            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- JAVASCRIPT -->
    <!-- ========================================================= -->

    <script src="{{ asset('js/auth.js') }}"></script>

</body>

</html>