<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>KPI baholash tizimi</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="{{ url('/assets/images/logo.png') }}"/>

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --secondary-color: #06b6d4;
            --accent-color: #8b5cf6;
            --success-color: #10b981;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
            --border-color: #e5e7eb;
            --shadow-light: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-large: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* New geometric pattern background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(45deg, #f0f9ff 25%, transparent 25%),
                linear-gradient(-45deg, #f0f9ff 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #f0f9ff 75%),
                linear-gradient(-45deg, transparent 75%, #f0f9ff 75%);
            background-size: 60px 60px;
            background-position: 0 0, 0 30px, 30px -30px, -30px 0px;
            opacity: 0.5;
            z-index: -2;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 25% 25%, rgba(79, 70, 229, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        .container-scroller {
            position: relative;
            z-index: 1;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            box-shadow: var(--shadow-large);
            border: 1px solid var(--border-color);
            width: 100%;
            max-width: 440px;
            position: relative;
            animation: slideUp 0.6s ease-out;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 20px 20px 0 0;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            width: 180px;
            height: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 2rem;
        }

        .welcome-text h2 {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .form-control {
            background: var(--bg-light);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.875rem 1rem 0.875rem 3rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: none;
            width: 100%;
        }

        .form-control:focus {
            background: var(--white);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--error-color);
            background: #fef2f2;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-light);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1.1rem;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .form-control.is-invalid + .input-icon {
            color: var(--error-color);
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--error-color);
            background: #fef2f2;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            border-left: 3px solid var(--error-color);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            animation: slideDown 0.3s ease-out;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left: 4px solid var(--error-color);
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid var(--success-color);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            color: var(--white);
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .divider {
            margin: 2rem 0 1.5rem 0;
            position: relative;
            text-align: center;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
        }

        .footer-text {
            text-align: center;
            color: var(--text-light);
            font-size: 0.85rem;
            line-height: 1.5;
            margin-top: 1rem;
        }

        .footer-text strong {
            color: var(--text-dark);
        }

        /* Loading spinner */
        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid var(--white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .login-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
                border-radius: 16px;
            }

            .welcome-text h2 {
                font-size: 1.5rem;
            }

            .logo-container img {
                width: 150px;
            }
        }

        /* Focus visible for accessibility */
        .btn-login:focus-visible,
        .form-control:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Old input value retention styling */
        .form-control.has-value {
            background: var(--white);
        }
    </style>
</head>
<body>
<div class="container-scroller">
    <div class="auth-wrapper">
        <div class="login-card">
            <div class="logo-container">
                <img src="{{ url('/assets/images/logo2.png') }}" alt="KPI Logo">
            </div>

            <div class="welcome-text">
                <h2>Xush kelibsiz!</h2>
                <p>KPI baholash tizimiga kirish uchun ma'lumotlaringizni kiriting</p>
            </div>

            {{-- Display general error messages --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                    @if ($errors->count() > 1)
                        <strong>Quyidagi xatoliklar mavjud:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $errors->first() }}
                    @endif
                </div>
            @endif

            {{-- Display success message if any --}}
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="mdi mdi-check-circle-outline me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Display status message if any --}}
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="mdi mdi-information-outline me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('authenticate') }}" method="POST" id="loginForm">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="username">Foydalanuvchi nomi</label>
                    <div class="input-group">
                        <input
                            id="username"
                            name="username"
                            type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            placeholder="Foydalanuvchi nomingizni kiriting"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                        <i class="mdi mdi-account-outline input-icon"></i>
                    </div>
                    @error('username')
                    <div class="invalid-feedback">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Parol</label>
                    <div class="input-group">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Parolingizni kiriting"
                            autocomplete="current-password"
                            required
                        >
                        <i class="mdi mdi-lock-outline input-icon"></i>
                    </div>
                    @error('password')
                    <div class="invalid-feedback">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <span class="btn-text">Tizimga kirish</span>
                    </button>
                </div>
            </form>

            <div class="divider"></div>

            <p class="footer-text">
                <strong>Copyright © {{ date('Y') }} ECOEKSPERTIZA.UZ</strong><br>
                Barcha huquqlar himoyalangan.
            </p>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const inputs = document.querySelectorAll('.form-control');

        // Add loading state to button on form submit
        form.addEventListener('submit', function() {
            loginBtn.classList.add('loading');
            loginBtn.querySelector('.btn-text').textContent = 'Yuklanmoqda...';
            loginBtn.disabled = true;
        });

        // Handle input interactions
        inputs.forEach(input => {
            // Check if input has value on page load (for old() values)
            if (input.value.length > 0) {
                input.classList.add('has-value');
            }

            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });

            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }

                // Remove error state when user starts typing
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentElement.parentElement.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                    }
                }
            });
        });

        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON') {
                const currentInput = document.activeElement;
                const inputs = Array.from(document.querySelectorAll('.form-control'));
                const currentIndex = inputs.indexOf(currentInput);

                if (currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                } else {
                    loginBtn.click();
                }
            }
        });

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    });
</script>
</body>
</html>
