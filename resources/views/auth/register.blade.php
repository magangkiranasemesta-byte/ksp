<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Equipment Maintenance System</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>

    <div class="auth-wrapper">

        <!-- SISI KIRI: BRANDING PERUSAHAAN -->
        <div class="auth-brand-side">
            <div class="brand-header">
                <div class="brand-icon">EMS</div>
                <div class="brand-name">Enterprise System</div>
            </div>

            <div class="brand-body">
                <span class="system-badge">Maintenance Control Center</span>
                <h1 class="brand-title">Reliable Asset & Equipment Management</h1>
                <p class="brand-description">
                    Kelola tiket perbaikan, pemeliharaan preventif, serta pemantauan suku cadang peralatan dalam satu platform terintegrasi.
                </p>
            </div>

            <div class="brand-footer">
                &copy; {{ date('Y') }} PT Equipment Maintenance System. All rights reserved.
            </div>
        </div>

        <!-- SISI KANAN: FORM REGISTRASI -->
        <div class="auth-form-side">
            <div class="form-container">

                <div class="form-header">
                    <h2 class="form-title">Create Account</h2>
                    <p class="form-subtitle">Register a new user account to access the system</p>
                </div>

                <!-- FORM -->
                <form action="{{ route('register.store') }}" method="POST">
                    @csrf

                    <!-- NAME + USERNAME -->
                    <div class="two-columns">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <div class="input-box">
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <div class="input-box">
                                <input type="text" name="username" value="{{ old('username') }}" placeholder="johndoe" required>
                            </div>
                        </div>
                    </div>

                    <!-- EMAIL + ROLE -->
                    <div class="two-columns">
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <div class="input-box">
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <div class="input-box">
                                <select name="role" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                                    <option value="SUPERADMIN" {{ old('role') == 'SUPERADMIN' ? 'selected' : '' }}>SUPERADMIN</option>
                                    <option value="ADMIN" {{ old('role') == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                                    <option value="SUPERVISOR" {{ old('role') == 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR</option>
                                    <option value="MANAGER" {{ old('role') == 'MANAGER' ? 'selected' : '' }}>MANAGER</option>
                                    <option value="ENGINEER" {{ old('role') == 'ENGINEER' ? 'selected' : '' }}>ENGINEER</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div class="two-columns">
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="input-box">
                                <input type="password" name="password" id="password" placeholder="••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">Show</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-box">
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">Show</button>
                            </div>
                        </div>
                    </div>

                    <!-- ERRORS -->
                    @if ($errors->any())
                        <div class="error-message">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- TERMS -->
                    <label class="terms">
                        <input type="checkbox" name="terms" required>
                        <span>I agree to the Terms of Service and Privacy Policy</span>
                    </label>

                    <!-- BUTTON -->
                    <button type="submit" class="btn-primary">
                        Create Account
                    </button>

                </form>

                <!-- LOGIN REDIRECT -->
                <div class="auth-redirect">
                    Already have an account? <a href="{{ route('login') }}">Log In</a>
                </div>

            </div>
        </div>

    </div>

    <script src="{{ asset('js/auth.js') }}"></script>
</body>

</html>