<x-filament-panels::page.simple>
    <style>
        html,
        body {
            min-height: 100%;
            color-scheme: light !important;
        }

        .fi-simple-layout {
            min-height: 100vh;
            background:
                radial-gradient(circle at 93% 70%, rgba(94, 142, 96, 0.22), transparent 13%),
                radial-gradient(circle at 97% 78%, rgba(79, 111, 82, 0.18), transparent 18%),
                linear-gradient(135deg, #f5f5f0 0%, #eeeeea 45%, #f8f8f4 100%) !important;
        }

        .fi-simple-header {
            display: none !important;
        }

        .fi-simple-main {
            width: 100%;
            max-width: 1080px !important;
            padding: 24px !important;
        }

        .login-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 48px);
        }

        .login-card {
            width: 100%;
            max-width: 980px;
            min-height: 540px;
            display: grid;
            grid-template-columns: 1fr 1.45fr;
            overflow: hidden;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.16);
        }

        .login-image {
            position: relative;
            min-height: 540px;
            background:
                linear-gradient(to bottom, rgba(0, 0, 0, 0.04), rgba(0, 0, 0, 0.35)),
                url("https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=85");
            background-size: cover;
            background-position: center;
        }

        .login-quote {
            position: absolute;
            left: 38px;
            bottom: 48px;
            color: #ffffff;
            font-size: 21px;
            font-style: italic;
            font-weight: 600;
            text-shadow: 0 4px 14px rgba(0, 0, 0, 0.45);
        }

        .login-content {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 46px 48px;
            background: #ffffff;
        }

        .login-box {
            width: 100%;
            max-width: 330px;
            color: #111827 !important;
        }

        .login-title {
            margin-bottom: 8px;
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #333333 !important;
        }

        .login-subtitle {
            margin-bottom: 28px;
            text-align: center;
            font-size: 12px;
            line-height: 1.7;
            color: #6b7280 !important;
        }

        .login-system-name {
            margin-bottom: 18px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #16a34a !important;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        /*
        |--------------------------------------------------------------------------
        | PERBAIKAN WARNA FORM FILAMENT
        |--------------------------------------------------------------------------
        */

        .login-box .fi-fo-field-wrp-label,
        .login-box .fi-fo-field-wrp-label span,
        .login-box label,
        .login-box .fi-label,
        .login-box .fi-label span {
            color: #374151 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
        }

        .login-box .fi-input-wrp {
            border-radius: 0 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            border: 1px solid #d7dce2 !important;
        }

        .login-box .fi-input-wrp:focus-within {
            border-color: #0ea5e9 !important;
            box-shadow: 0 0 0 1px #0ea5e9 !important;
        }

        .login-box input,
        .login-box .fi-input,
        .login-box input[type="text"],
        .login-box input[type="email"],
        .login-box input[type="password"] {
            min-height: 38px !important;
            border-radius: 0 !important;
            border: none !important;
            background: #ffffff !important;
            color: #111827 !important;
            -webkit-text-fill-color: #111827 !important;
            caret-color: #111827 !important;
            font-size: 13px !important;
            box-shadow: none !important;
        }

        .login-box input::placeholder,
        .login-box .fi-input::placeholder {
            color: #9ca3af !important;
            -webkit-text-fill-color: #9ca3af !important;
            opacity: 1 !important;
        }

        .login-box input:-webkit-autofill,
        .login-box input:-webkit-autofill:hover,
        .login-box input:-webkit-autofill:focus {
            -webkit-text-fill-color: #111827 !important;
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
            transition: background-color 9999s ease-in-out 0s;
        }

        .login-box .fi-input-wrp svg,
        .login-box .fi-icon-btn,
        .login-box .fi-icon-btn svg {
            color: #6b7280 !important;
        }

        .login-box .fi-fo-field-wrp {
            margin-bottom: 12px !important;
        }

        .login-box .fi-fo-field-wrp-helper-text,
        .login-box .fi-fo-field-wrp-hint,
        .login-box .fi-fo-field-wrp-hint a {
            color: #6b7280 !important;
            font-size: 11px !important;
        }

        .login-box .fi-fo-field-wrp-error-message {
            color: #dc2626 !important;
            font-size: 11px !important;
        }

        .login-box .fi-checkbox-input {
            color: #16a34a !important;
            border-color: #d1d5db !important;
            background-color: #ffffff !important;
        }

        .login-box .fi-checkbox-input:checked {
            background-color: #16a34a !important;
            border-color: #16a34a !important;
        }

        .login-box .fi-checkbox-list-option-label,
        .login-box .fi-checkbox-label,
        .login-box .fi-fo-checkbox-list-option-label {
            color: #374151 !important;
            font-size: 12px !important;
        }

        .forgot-link {
            margin-top: -4px;
            margin-bottom: 16px;
            text-align: right;
        }

        .forgot-link a {
            font-size: 11px;
            color: #3b82f6 !important;
            text-decoration: none;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            min-height: 42px;
            margin-top: 10px;
            border: none;
            border-radius: 0;
            background: #15c400;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .login-button:hover {
            background: #12a900;
            color: #ffffff !important;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: #71717a !important;
            font-size: 11px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #e5e7eb;
        }

        .social-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .social-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 36px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569 !important;
            -webkit-text-fill-color: #475569 !important;
            font-size: 11px;
            text-decoration: none;
        }

        .social-button span {
            font-size: 14px;
            font-weight: 700;
        }

        .login-footer {
            margin-top: 22px;
            text-align: center;
            font-size: 11px;
            color: #71717a !important;
        }

        .login-footer a {
            color: #15c400 !important;
            font-weight: 700;
            text-decoration: none;
        }

        .decor-plant {
            position: fixed;
            right: -50px;
            bottom: -50px;
            width: 220px;
            height: 220px;
            border-radius: 42% 58% 50% 50%;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent),
                radial-gradient(circle at 35% 35%, #9fbd8f, #4f734f 55%, #24402d 100%);
            opacity: 0.45;
            transform: rotate(-20deg);
            pointer-events: none;
        }

        @media (max-width: 900px) {
            .login-card {
                max-width: 520px;
                grid-template-columns: 1fr;
            }

            .login-image {
                min-height: 260px;
            }

            .login-content {
                padding: 42px 32px;
            }
        }

        @media (max-width: 640px) {
            .fi-simple-main {
                padding: 16px !important;
            }

            .login-wrapper {
                min-height: calc(100vh - 32px);
            }

            .login-card {
                min-height: auto;
            }

            .login-image {
                display: none;
            }

            .login-content {
                padding: 42px 26px;
            }

            .social-buttons {
                grid-template-columns: 1fr;
            }

            .decor-plant {
                display: none;
            }
        }

        .custom-login-form {
            width: 100%;
            color-scheme: light !important;
        }

        .custom-login-form .form-group {
            margin-bottom: 16px;
        }

        .custom-login-form .form-label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
            color: #111827 !important;
            -webkit-text-fill-color: #111827 !important;
        }

        .custom-login-form .form-label span {
            color: #ef4444 !important;
            -webkit-text-fill-color: #ef4444 !important;
        }

        .custom-login-form .form-input {
            width: 100%;
            height: 40px;
            padding: 0 13px;
            border: 1px solid #d7dce2;
            border-radius: 0;
            background: #ffffff !important;
            color: #111827 !important;
            -webkit-text-fill-color: #111827 !important;
            caret-color: #111827 !important;
            font-size: 13px;
            outline: none;
            box-shadow: none;
        }

        .custom-login-form .form-input::placeholder {
            color: #9ca3af !important;
            -webkit-text-fill-color: #9ca3af !important;
            opacity: 1;
        }

        .custom-login-form .form-input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 1px #0ea5e9;
        }

        .custom-login-form .form-input:-webkit-autofill,
        .custom-login-form .form-input:-webkit-autofill:hover,
        .custom-login-form .form-input:-webkit-autofill:focus {
            -webkit-text-fill-color: #111827 !important;
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
            box-shadow: 0 0 0 1000px #ffffff inset !important;
        }

        .custom-login-form .form-error {
            margin-top: 6px;
            font-size: 11px;
            color: #dc2626 !important;
            -webkit-text-fill-color: #dc2626 !important;
        }
    </style>

    <div class="decor-plant"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-image">
                <div class="login-quote">
                    “Espress Yourself.”
                </div>
            </div>

            <div class="login-content">
                <div class="login-box">
                    <div class="login-system-name">
                        Cafe Second
                    </div>

                    <h1 class="login-title">
                        Welcome Back
                    </h1>

                    <p class="login-subtitle">
                        Enter your account credentials to manage orders.
                    </p>

                    <form class="custom-login-form" wire:submit="authenticate">
                        <div class="form-group">
                            <label class="form-label" for="email">
                                Email <span>*</span>
                            </label>

                            <input autocomplete="email" autofocus class="form-input" id="email"
                                placeholder="Masukkan email" type="email" wire:model="data.email">

                            @error('data.email')
                                <div class="form-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">
                                Password <span>*</span>
                            </label>

                            <input autocomplete="current-password" class="form-input" id="password"
                                placeholder="Masukkan password" type="password" wire:model="data.password">

                            @error('data.password')
                                <div class="form-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="forgot-link">
                            <a href="#">
                                Forgot your password?
                            </a>
                        </div>

                        <button class="login-button" type="submit">
                            <span wire:loading.remove>
                                Log In
                            </span>

                            <span wire:loading>
                                Loading...
                            </span>
                        </button>
                    </form>

                    <div class="login-footer">
                        Sistem Pemesanan Online Berbasis QR Code<br>
                        <a href="#">Cafe Second</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
