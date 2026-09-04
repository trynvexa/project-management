<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fffdf5">
    <title>Create account · Project Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page auth-register">
    <main class="auth-shell">
        <section class="auth-intro" aria-label="Project Management">
            <a href="{{ route('login') }}" class="auth-brand">
                <span aria-hidden="true">PM</span><b>Project Management</b>
            </a>
            <div class="auth-intro-copy">
                <p class="eyebrow">GET STARTED</p>
                <h1>Turn good plans into visible progress.</h1>
                <p>Create your account, then bring your team and projects together.</p>
            </div>
            <ul class="auth-checklist" aria-label="Product benefits">
                <li>Structured project work</li>
                <li>Simple team collaboration</li>
                <li>Built for daily momentum</li>
            </ul>
        </section>
        <section class="auth-card-wrap">
            <div class="auth-card">
                <a href="{{ route('login') }}" class="auth-brand auth-brand-mobile">
                    <span aria-hidden="true">PM</span><b>Project Management</b>
                </a>
                <div class="auth-card-heading">
                    <p class="eyebrow">GET STARTED</p>
                    <h1>Create account</h1>
                    <p class="auth-copy">Set up your personal account in a minute.</p>
                </div>
                @if ($errors->any())
                    <div class="form-alert" role="alert">{{ $errors->first() }}</div>
                @endif
                <form action="{{ route('register') }}" method="POST" class="auth-form">
                    @csrf
                    <div>
                        <label class="form-label" for="name">Full name</label>
                        <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
                    </div>
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                    </div>
                    <div>
                        <label class="form-label" for="password">Password</label>
                        <input id="password" class="form-input" type="password" name="password" autocomplete="new-password" required>
                    </div>
                    <div>
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" autocomplete="new-password" required>
                    </div>
                    <button type="submit" class="btn-primary w-full" data-loading="Creating account…">Create account <span aria-hidden="true">→</span></button>
                </form>
                <p class="auth-footer">Already have an account? <a class="text-link" href="{{ route('login') }}">Sign in</a></p>
            </div>
        </section>
    </main>
</body>
</html>
