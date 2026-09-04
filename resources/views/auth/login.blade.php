<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fffdf5">
    <title>Sign in · Project Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page auth-login">
    <main class="auth-shell">
        <section class="auth-intro" aria-label="Project Management">
            <a href="{{ route('login') }}" class="auth-brand">
                <span aria-hidden="true">PM</span><b>Project Management</b>
            </a>
            <div class="auth-intro-copy">
                <p class="eyebrow">YOUR WORK, IN SYNC</p>
                <h1>Make every project feel under control.</h1>
                <p>Keep tasks, people, and progress in one clear workspace.</p>
            </div>
            <ul class="auth-checklist" aria-label="Product benefits">
                <li>Clear project ownership</li>
                <li>Live task progress</li>
                <li>Team-ready workspace</li>
            </ul>
        </section>
        <section class="auth-card-wrap">
            <div class="auth-card">
                <a href="{{ route('login') }}" class="auth-brand auth-brand-mobile">
                    <span aria-hidden="true">PM</span><b>Project Management</b>
                </a>
                <div class="auth-card-heading">
                    <p class="eyebrow">WELCOME BACK</p>
                    <h1>Sign in</h1>
                    <p class="auth-copy">Pick up where your team left off.</p>
                </div>
                @if ($errors->any())
                    <div class="form-alert" role="alert">{{ $errors->first() }}</div>
                @endif
                <form action="{{ route('login') }}" method="POST" class="auth-form">
                    @csrf
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="you@example.com">
                    </div>
                    <div>
                        <div class="auth-label-row">
                            <label class="form-label" for="password">Password</label>
                            <a class="text-link" href="{{ route('password.request') }}">Forgot password?</a>
                        </div>
                        <input id="password" class="form-input" type="password" name="password" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn-primary w-full" data-loading="Signing in…">Sign in to workspace <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-footer">New here? <a class="text-link" href="{{ route('register') }}">Create an account</a></p>
            </div>
        </section>
    </main>
</body>
</html>
